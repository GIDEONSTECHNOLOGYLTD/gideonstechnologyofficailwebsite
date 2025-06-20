<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Query Performance Statistics</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="p-3">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Queries</h5>
                                        <h2><?= $queryStats['total_queries'] ?></h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Time</h5>
                                        <h2><?= number_format($queryStats['total_time'], 2) ?> ms</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Average Time</h5>
                                        <h2><?= number_format($queryStats['avg_time'], 2) ?> ms</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Slow Queries</h5>
                                        <h2><?= $queryStats['slow_queries'] ?></h2>
                                        <small>Threshold: <?= $queryStats['slow_query_threshold'] ?> ms</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>System Information</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <tbody>
                                <?php foreach ($systemStats as $key => $value): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex px-3 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm"><?= ucwords(str_replace('_', ' ', $key)) ?></h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold"><?= $value ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Query Details</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Query</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Duration</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Rows</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($queries as $query): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex px-3 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm"><?= htmlspecialchars($query['query']) ?></h6>
                                                <p class="text-xs text-secondary mb-0">
                                                    <?php if (!empty($query['params'])): ?>
                                                    <small>Params: <?= htmlspecialchars(json_encode($query['params'])) ?></small>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">
                                            <?php if (isset($query['duration'])): ?>
                                                <?= number_format($query['duration'], 2) ?> ms
                                            <?php else: ?>
                                                Not completed
                                            <?php endif; ?>
                                        </p>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <?php if (isset($query['success'])): ?>
                                            <?php if ($query['success']): ?>
                                                <span class="badge badge-sm bg-gradient-success">Success</span>
                                            <?php else: ?>
                                                <span class="badge badge-sm bg-gradient-danger">Failed</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge badge-sm bg-gradient-secondary">Unknown</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            <?= $query['row_count'] ?? 'N/A' ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12 text-center">
            <a href="/admin/performance/indexes" class="btn btn-primary">Analyze Database Indexes</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
