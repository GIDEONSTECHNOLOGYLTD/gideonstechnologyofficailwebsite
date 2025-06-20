<?php

return [
    'get_services' => 'SELECT * FROM services WHERE category = ? AND is_featured = 1 ORDER BY sort_order ASC',
    'get_detailed_services' => 'SELECT * FROM services WHERE category = ? ORDER BY sort_order ASC',
    'get_featured_projects' => 'SELECT * FROM projects WHERE category = ? AND is_featured = 1 ORDER BY created_at DESC LIMIT 6',
    'get_all_projects' => 'SELECT * FROM projects WHERE category = ? ORDER BY created_at DESC',
    'get_project_by_slug' => 'SELECT * FROM projects WHERE category = ? AND slug = ?',
    'get_templates' => 'SELECT * FROM templates WHERE is_active = 1 ORDER BY created_at DESC',
    'get_templates_by_category' => 'SELECT * FROM templates WHERE category = ? AND is_active = 1 ORDER BY created_at DESC',
    'get_template' => 'SELECT * FROM templates WHERE id = ? AND is_active = 1',
    'purchase_template' => 'INSERT INTO template_purchases (user_id, template_id, purchase_date, status) VALUES (?, ?, CURRENT_TIMESTAMP, ?)',
    'update_template_purchases' => 'UPDATE templates SET purchases = purchases + 1 WHERE id = ?',
    'get_user_purchased_templates' => 'SELECT t.* FROM templates t INNER JOIN template_purchases p ON t.id = p.template_id WHERE p.user_id = ? ORDER BY p.purchase_date DESC',
    'get_template_categories' => 'SELECT DISTINCT category FROM templates WHERE is_active = 1 ORDER BY category ASC',
    'create_quote' => 'INSERT INTO quotes (name, email, phone, project_type, description, budget, timeline, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
];
