<?php
class Paginator {
    private $page;
    private $perPage;
    private $total;
    private $maxPerPage;
    private $pages;
    private $offset;

    public function __construct($total, $page = 1, $perPage = null) {
        $this->total = max(0, (int)$total);
        $this->maxPerPage = MAX_PER_PAGE;
        $this->perPage = $this->validatePerPage($perPage ?? DEFAULT_PER_PAGE);
        $this->pages = max(1, ceil($this->total / $this->perPage));
        $this->page = $this->validatePage($page);
        $this->offset = ($this->page - 1) * $this->perPage;
    }

    private function validatePage($page) {
        $page = max(1, (int)$page);
        return min($page, $this->pages);
    }

    private function validatePerPage($perPage) {
        $perPage = max(1, (int)$perPage);
        return min($perPage, $this->maxPerPage);
    }

    public function getOffset() {
        return $this->offset;
    }

    public function getLimit() {
        return $this->perPage;
    }

    public function getCurrentPage() {
        return $this->page;
    }

    public function getTotalPages() {
        return $this->pages;
    }

    public function getTotalItems() {
        return $this->total;
    }

    public function hasNextPage() {
        return $this->page < $this->pages;
    }

    public function hasPreviousPage() {
        return $this->page > 1;
    }

    public function getNextPage() {
        return $this->hasNextPage() ? $this->page + 1 : null;
    }

    public function getPreviousPage() {
        return $this->hasPreviousPage() ? $this->page - 1 : null;
    }

    public function getPageRange($range = 5) {
        $start = max(1, $this->page - floor($range / 2));
        $end = min($this->pages, $start + $range - 1);
        
        if ($end - $start + 1 < $range) {
            $start = max(1, $end - $range + 1);
        }

        return range($start, $end);
    }

    public function getSQLLimit() {
        return "LIMIT {$this->offset}, {$this->perPage}";
    }

    public function toArray() {
        return [
            'current_page' => $this->page,
            'per_page' => $this->perPage,
            'total' => $this->total,
            'total_pages' => $this->pages,
            'has_next' => $this->hasNextPage(),
            'has_previous' => $this->hasPreviousPage(),
            'next_page' => $this->getNextPage(),
            'previous_page' => $this->getPreviousPage()
        ];
    }

    public function renderHTML() {
        $html = '<nav class="pagination">';
        $html .= '<ul class="pagination-list">';

        // Previous button
        if ($this->hasPreviousPage()) {
            $html .= '<li><a href="?page=' . $this->getPreviousPage() . '">&laquo; Previous</a></li>';
        }

        // Page numbers
        foreach ($this->getPageRange() as $p) {
            $class = $p === $this->page ? ' active' : '';
            $html .= '<li class="' . $class . '"><a href="?page=' . $p . '">' . $p . '</a></li>';
        }

        // Next button
        if ($this->hasNextPage()) {
            $html .= '<li><a href="?page=' . $this->getNextPage() . '">Next &raquo;</a></li>';
        }

        $html .= '</ul>';
        $html .= '</nav>';

        return $html;
    }

    public function getMetadata() {
        $start = $this->offset + 1;
        $end = min($this->offset + $this->perPage, $this->total);

        return [
            'showing_from' => $start,
            'showing_to' => $end,
            'total' => $this->total,
            'current_page' => $this->page,
            'last_page' => $this->pages
        ];
    }
}