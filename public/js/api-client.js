/**
 * API Client for Gideons Technology
 * Use this file to test API endpoints from the browser console
 */

// API base URL - change this to match your server
const API_BASE_URL = 'http://localhost:8080';

// API client object
const GtechAPI = {
    // Get templates
    getTemplates: async function() {
        try {
            const response = await fetch(`${API_BASE_URL}/web-dev/templates`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error(`API error: ${response.status}`);
            }
            
            return await response.json();
        } catch (error) {
            console.error('Error fetching templates:', error);
            throw error;
        }
    },
    
    // Get orders
    getOrders: async function() {
        try {
            const response = await fetch(`${API_BASE_URL}/orders`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error(`API error: ${response.status}`);
            }
            
            return await response.json();
        } catch (error) {
            console.error('Error fetching orders:', error);
            throw error;
        }
    }
};

// Usage examples:
// GtechAPI.getTemplates().then(data => console.log('Templates:', data));
// GtechAPI.getOrders().then(data => console.log('Orders:', data));