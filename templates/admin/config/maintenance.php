<?php $this->layout('admin/layout', ['page' => 'maintenance', 'messages' => $messages ?? []]); ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Maintenance Mode</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
        <li class="breadcrumb-item active">Maintenance Mode</li>
    </ol>
    
    <div class="row">
        <div class="col-xl-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-tools me-1"></i>
                    Maintenance Settings
                </div>
                <div class="card-body">
                    <form method="post" action="/admin/config/maintenance/update">
                        <div class="mb-4 row">
                            <div class="col-md-9">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="maintenance_mode" 
                                           name="maintenance_mode" <?= $maintenanceMode ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="maintenance_mode">
                                        <strong>Enable Maintenance Mode</strong>
                                    </label>
                                </div>
                                <div class="form-text text-muted mb-3">
                                    When enabled, regular visitors will see a maintenance page. Administrators can still access the site.
                                </div>
                                
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Warning:</strong> Enabling maintenance mode will make your website inaccessible to regular visitors.
                                    Be sure to disable it when maintenance is complete.
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="maintenance-icon">
                                    <i class="fas fa-hard-hat fa-5x <?= $maintenanceMode ? 'text-danger' : 'text-secondary' ?>"></i>
                                    <p class="mt-2 <?= $maintenanceMode ? 'text-danger fw-bold' : 'text-muted' ?>">
                                        <?= $maintenanceMode ? 'CURRENTLY ACTIVE' : 'INACTIVE' ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="maintenance_message" class="form-label">Maintenance Message</label>
                            <textarea class="form-control" id="maintenance_message" name="maintenance_message" 
                                      rows="4"><?= htmlspecialchars($maintenanceMessage) ?></textarea>
                            <div class="form-text text-muted">
                                This message will be displayed to visitors during maintenance mode.
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="/admin/config" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back to Configuration
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Save Maintenance Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xl-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-eye me-1"></i>
                    Maintenance Mode Preview
                </div>
                <div class="card-body">
                    <div class="maintenance-preview p-5 bg-light border rounded text-center">
                        <h2><i class="fas fa-tools me-2"></i> Maintenance Mode</h2>
                        <div class="my-4">
                            <?= nl2br(htmlspecialchars($maintenanceMessage)) ?>
                        </div>
                        <p class="text-muted">This is how your maintenance page will appear to visitors.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
