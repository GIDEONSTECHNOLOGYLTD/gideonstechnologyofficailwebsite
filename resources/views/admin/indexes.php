<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Database Index Analysis</h6>
                    <a href="/admin/performance" class="btn btn-sm btn-primary">Back to Performance Dashboard</a>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="p-3">
                        <h5>Index Recommendations</h5>
                        <?php if (empty($recommendations)): ?>
                            <div class="alert alert-success">No additional index recommendations found. Your database schema appears to be well-optimized.</div>
                        <?php else: ?>
                            <div class="alert alert-info">Found <?= count($recommendations) ?> tables with potential index optimizations.</div>
                            
                            <?php foreach ($recommendations as $tableName => $tableRecommendations): ?>
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h6>Table: <?= htmlspecialchars($tableName) ?></h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Column</th>
                                                    <th>Reason</th>
                                                    <th>SQL</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($tableRecommendations as $recommendation): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($recommendation['column']) ?></td>
                                                        <td><?= htmlspecialchars($recommendation['reason']) ?></td>
                                                        <td><code><?= htmlspecialchars($recommendation['sql']) ?></code></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Current Database Schema</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="accordion" id="tableAccordion">
                        <?php $counter = 0; ?>
                        <?php foreach ($tableData as $tableName => $data): ?>
                            <?php $counter++; ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading<?= $counter ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $counter ?>" aria-expanded="false" aria-controls="collapse<?= $counter ?>">
                                        Table: <?= htmlspecialchars($tableName) ?> (<?= count($data['columns']) ?> columns, <?= count($data['indexes']) ?> indexes)
                                    </button>
                                </h2>
                                <div id="collapse<?= $counter ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $counter ?>" data-bs-parent="#tableAccordion">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6>Columns</h6>
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Field</th>
                                                            <th>Type</th>
                                                            <th>Null</th>
                                                            <th>Key</th>
                                                            <th>Default</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($data['columns'] as $column): ?>
                                                            <tr>
                                                                <td><?= htmlspecialchars($column['Field']) ?></td>
                                                                <td><?= htmlspecialchars($column['Type']) ?></td>
                                                                <td><?= htmlspecialchars($column['Null']) ?></td>
                                                                <td><?= htmlspecialchars($column['Key']) ?></td>
                                                                <td><?= htmlspecialchars($column['Default'] ?? 'NULL') ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <h6>Indexes</h6>
                                                <?php if (empty($data['indexes'])): ?>
                                                    <div class="alert alert-warning">No indexes found for this table.</div>
                                                <?php else: ?>
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Key Name</th>
                                                                <th>Column</th>
                                                                <th>Non Unique</th>
                                                                <th>Type</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($data['indexes'] as $index): ?>
                                                                <tr>
                                                                    <td><?= htmlspecialchars($index['Key_name']) ?></td>
                                                                    <td><?= htmlspecialchars($index['Column_name']) ?></td>
                                                                    <td><?= htmlspecialchars($index['Non_unique']) ?></td>
                                                                    <td><?= htmlspecialchars($index['Index_type']) ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
