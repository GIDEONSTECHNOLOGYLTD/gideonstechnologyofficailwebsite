<?php $this->layout('admin/layout', ['page' => 'config', 'messages' => $messages ?? []]); ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">System Configuration</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
        <li class="breadcrumb-item active">Configuration</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-cogs me-1"></i>
            Configuration Settings
        </div>
        <div class="card-body">
            <!-- Category Tabs -->
            <ul class="nav nav-tabs mb-4">
                <?php foreach ($categories as $category): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $category === $currentCategory ? 'active' : '' ?>" 
                           href="/admin/config?category=<?= $category ?>">
                            <?= ucfirst($category) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            
            <!-- Configuration Form -->
            <form method="post" action="/admin/config/save">
                <input type="hidden" name="category" value="<?= $currentCategory ?>">
                
                <?php foreach ($configs as $key => $config): ?>
                    <div class="mb-3 row">
                        <label class="col-sm-3 col-form-label" for="<?= $key ?>">
                            <?= $config['display_name'] ?>
                        </label>
                        <div class="col-sm-9">
                            <?php if ($config['type'] === 'boolean'): ?>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="<?= $key ?>" 
                                           name="<?= $key ?>" <?= $config['value'] ? 'checked' : '' ?>>
                                    <input type="hidden" name="<?= $key ?>_type" value="boolean">
                                </div>
                            <?php elseif ($config['type'] === 'textarea'): ?>
                                <textarea class="form-control" id="<?= $key ?>" name="<?= $key ?>" 
                                          rows="3"><?= htmlspecialchars($config['value']) ?></textarea>
                            <?php elseif ($config['type'] === 'select' && isset($config['options'])): ?>
                                <select class="form-select" id="<?= $key ?>" name="<?= $key ?>">
                                    <?php foreach ($config['options'] as $value => $label): ?>
                                        <option value="<?= $value ?>" <?= $config['value'] === $value ? 'selected' : '' ?>>
                                            <?= $label ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <input type="text" class="form-control" id="<?= $key ?>" 
                                       name="<?= $key ?>" value="<?= htmlspecialchars($config['value']) ?>">
                            <?php endif; ?>
                            
                            <?php if (!empty($config['description'])): ?>
                                <div class="form-text text-muted">
                                    <?= $config['description'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="mb-3 row">
                    <div class="col-sm-9 offset-sm-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
