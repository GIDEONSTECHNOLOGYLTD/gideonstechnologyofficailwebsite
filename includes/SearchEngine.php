<?php
class SearchEngine {
    private $db;
    private $table;
    private $searchableFields;
    private $orderBy;
    private $paginator;

    public function __construct($table, $searchableFields, $orderBy = 'id DESC') {
        $this->db = Database::getInstance();
        $this->table = $table;
        $this->searchableFields = $searchableFields;
        $this->orderBy = $orderBy;
    }

    public function search($query, $filters = [], $page = 1, $perPage = DEFAULT_PER_PAGE) {
        $whereClauses = [];
        $params = [];
        $types = '';

        // Process search query
        if (!empty($query)) {
            $searchConditions = [];
            foreach ($this->searchableFields as $field) {
                $searchConditions[] = "$field LIKE ?";
                $params[] = "%$query%";
                $types .= 's';
            }
            $whereClauses[] = '(' . implode(' OR ', $searchConditions) . ')';
        }

        // Process filters
        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                $whereClauses[] = "$field = ?";
                $params[] = $value;
                $types .= $this->getParameterType($value);
            }
        }

        // Build query
        $where = empty($whereClauses) ? '' : 'WHERE ' . implode(' AND ', $whereClauses);
        
        // Get total count
        $countQuery = "SELECT COUNT(*) as total FROM {$this->table} $where";
        $stmt = $this->db->prepare($countQuery);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $total = $stmt->get_result()->fetch_assoc()['total'];

        // Create paginator
        $this->paginator = new Paginator($total, $page, $perPage);

        // Get results
        $query = "SELECT * FROM {$this->table} $where ORDER BY {$this->orderBy} " . 
                 $this->paginator->getSQLLimit();
        
        $stmt = $this->db->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return [
            'results' => $results,
            'pagination' => $this->paginator->toArray()
        ];
    }

    public function suggest($query, $field, $limit = 5) {
        $stmt = $this->db->prepare(
            "SELECT DISTINCT $field 
             FROM {$this->table} 
             WHERE $field LIKE ? 
             LIMIT ?"
        );
        $query = "%$query%";
        $stmt->bind_param('si', $query, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function facetedSearch($query, $facets = [], $page = 1, $perPage = DEFAULT_PER_PAGE) {
        $params = [];
        $types = '';
        $whereClauses = [];
        $facetCounts = [];

        // Process search query
        if (!empty($query)) {
            $searchConditions = [];
            foreach ($this->searchableFields as $field) {
                $searchConditions[] = "$field LIKE ?";
                $params[] = "%$query%";
                $types .= 's';
            }
            $whereClauses[] = '(' . implode(' OR ', $searchConditions) . ')';
        }

        // Process facets
        foreach ($facets as $field => $values) {
            if (!empty($values)) {
                $facetConditions = [];
                foreach ((array)$values as $value) {
                    $facetConditions[] = "$field = ?";
                    $params[] = $value;
                    $types .= $this->getParameterType($value);
                }
                $whereClauses[] = '(' . implode(' OR ', $facetConditions) . ')';
            }
        }

        $where = empty($whereClauses) ? '' : 'WHERE ' . implode(' AND ', $whereClauses);

        // Get facet counts
        foreach ($this->searchableFields as $field) {
            $facetQuery = "SELECT $field, COUNT(*) as count 
                          FROM {$this->table} 
                          $where 
                          GROUP BY $field";
            $stmt = $this->db->prepare($facetQuery);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $facetCounts[$field] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }

        // Get search results
        return [
            'results' => $this->search($query, $facets, $page, $perPage),
            'facets' => $facetCounts
        ];
    }

    private function getParameterType($value) {
        if (is_int($value)) return 'i';
        if (is_double($value)) return 'd';
        return 's';
    }

    public function getPaginator() {
        return $this->paginator;
    }
}