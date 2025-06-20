<?php require APP_PATH . '/views/layouts/header.php'; ?>

<div class="api-documentation">
    <div class="container">
        <div class="api-header">
            <h1>API Documentation</h1>
            <p>Documentation for the GStore API endpoints</p>
        </div>

        <div class="api-section">
            <h2>Authentication</h2>
            <div class="api-endpoint">
                <h3>POST /api/auth/login</h3>
                <div class="endpoint-details">
                    <p><strong>Description:</strong> Login to get authentication token</p>
                    <div class="request">
                        <h4>Request</h4>
                        <pre>
{
    "email": "string",
    "password": "string"
}
                        </pre>
                    </div>
                    <div class="response">
                        <h4>Response</h4>
                        <pre>
{
    "token": "string",
    "user": {
        "id": "int",
        "email": "string",
        "name": "string"
    }
}
                        </pre>
                    </div>
                </div>
            </div>

            <div class="api-endpoint">
                <h3>POST /api/auth/register</h3>
                <div class="endpoint-details">
                    <p><strong>Description:</strong> Register a new user</p>
                    <div class="request">
                        <h4>Request</h4>
                        <pre>
{
    "email": "string",
    "password": "string",
    "name": "string",
    "phone": "string"
}
                        </pre>
                    </div>
                    <div class="response">
                        <h4>Response</h4>
                        <pre>
{
    "token": "string",
    "user": {
        "id": "int",
        "email": "string",
        "name": "string"
    }
}
                        </pre>
                    </div>
                </div>
            </div>
        </div>

        <div class="api-section">
            <h2>Products</h2>
            <div class="api-endpoint">
                <h3>GET /api/products</h3>
                <div class="endpoint-details">
                    <p><strong>Description:</strong> Get list of products</p>
                    <div class="request">
                        <h4>Query Parameters</h4>
                        <ul>
                            <li>category: string (optional)</li>
                            <li>page: int (optional)</li>
                            <li>limit: int (optional)</li>
                        </ul>
                    </div>
                    <div class="response">
                        <h4>Response</h4>
                        <pre>
{
    "products": [
        {
            "id": "int",
            "name": "string",
            "price": "float",
            "description": "string",
            "images": ["string"],
            "category": "string",
            "stock": "int"
        }
    ],
    "pagination": {
        "total": "int",
        "page": "int",
        "limit": "int"
    }
}
                        </pre>
                    </div>
                </div>
            </div>

            <div class="api-endpoint">
                <h3>GET /api/products/{id}</h3>
                <div class="endpoint-details">
                    <p><strong>Description:</strong> Get single product details</p>
                    <div class="response">
                        <h4>Response</h4>
                        <pre>
{
    "product": {
        "id": "int",
        "name": "string",
        "price": "float",
        "description": "string",
        "images": ["string"],
        "category": "string",
        "stock": "int",
        "reviews": [
            {
                "id": "int",
                "user": "string",
                "rating": "int",
                "comment": "string"
            }
        ]
    }
}
                        </pre>
                    </div>
                </div>
            </div>
        </div>

        <div class="api-section">
            <h2>Cart</h2>
            <div class="api-endpoint">
                <h3>GET /api/cart</h3>
                <div class="endpoint-details">
                    <p><strong>Description:</strong> Get user's cart</p>
                    <div class="response">
                        <h4>Response</h4>
                        <pre>
{
    "cart": {
        "items": [
            {
                "product_id": "int",
                "quantity": "int",
                "price": "float",
                "subtotal": "float"
            }
        ],
        "total": "float",
        "discount": "float",
        "final_total": "float"
    }
}
                        </pre>
                    </div>
                </div>
            </div>

            <div class="api-endpoint">
                <h3>POST /api/cart/items</h3>
                <div class="endpoint-details">
                    <p><strong>Description:</strong> Add item to cart</p>
                    <div class="request">
                        <h4>Request</h4>
                        <pre>
{
    "product_id": "int",
    "quantity": "int"
}
                        </pre>
                    </div>
                </div>
            </div>
        </div>

        <div class="api-section">
            <h2>Orders</h2>
            <div class="api-endpoint">
                <h3>POST /api/orders</h3>
                <div class="endpoint-details">
                    <p><strong>Description:</strong> Create new order</p>
                    <div class="request">
                        <h4>Request</h4>
                        <pre>
{
    "shipping_address": {
        "name": "string",
        "address": "string",
        "city": "string",
        "state": "string",
        "zip": "string"
    },
    "payment_method": "string",
    "items": [
        {
            "product_id": "int",
            "quantity": "int"
        }
    ]
}
                        </pre>
                    </div>
                    <div class="response">
                        <h4>Response</h4>
                        <pre>
{
    "order": {
        "id": "int",
        "order_number": "string",
        "total": "float",
        "status": "string",
        "created_at": "string"
    }
}
                        </pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.api-documentation {
    padding: 40px 0;
}

.api-header {
    text-align: center;
    margin-bottom: 50px;
}

.api-header h1 {
    font-size: 2.5em;
    color: #2c3e50;
    margin-bottom: 15px;
}

.api-section {
    margin-bottom: 50px;
}

.api-section h2 {
    color: #2c3e50;
    margin-bottom: 30px;
    border-bottom: 2px solid #3498db;
    padding-bottom: 10px;
}

.api-endpoint {
    background: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 30px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.api-endpoint h3 {
    color: #3498db;
    margin-bottom: 20px;
}

.endpoint-details {
    margin-top: 20px;
}

.endpoint-details h4 {
    color: #2c3e50;
    margin-bottom: 15px;
}

.endpoint-details p {
    color: #666;
    margin-bottom: 20px;
}

.endpoint-details ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.endpoint-details li {
    margin-bottom: 10px;
    color: #666;
}

.endpoint-details pre {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 5px;
    overflow-x: auto;
    margin: 20px 0;
}

.endpoint-details code {
    font-family: 'Courier New', monospace;
    color: #2c3e50;
}

@media (max-width: 768px) {
    .api-section {
        margin-bottom: 30px;
    }
    
    .api-endpoint {
        padding: 15px;
    }
    
    .endpoint-details pre {
        margin: 15px 0;
    }
}
</style>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
