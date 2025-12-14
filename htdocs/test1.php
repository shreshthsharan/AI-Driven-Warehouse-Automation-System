<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Warehouse Automation Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <style>
        /* Custom scrollbar for better aesthetics in scrollable areas */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #555;
            border-radius: 10px;
        }

        /* Styling for the alert toggle switch */
        #alerts-toggle:checked + div + .dot {
            transform: translateX(1.5rem); /* Moves the dot to the right */
            background-color: #34D399; /* Green when active */
        }

        #alerts-toggle:checked + div {
            background-color: #6ee7b7; /* Lighter green for the track */
        }

        /* Custom style for RFID charts to prevent animation conflicts */
        .rfid-chart-canvas canvas {
            transition: none !important;
            animation: none !important;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-800">

    <div id="app-container" class="min-h-screen p-4">
        <div class="container mx-auto bg-white rounded-xl shadow-lg p-6 md:p-8">
            <header class="mb-8 text-center">
                <h1 class="text-3xl md:text-4xl font-extrabold text-blue-800 mb-2">
                    Smart Warehouse Automation Dashboard
                </h1>
                <p class="text-lg text-gray-600">Real-time Insights & Predictive Analytics</p>
            </header>

            <div id="loader" class="flex items-center justify-center h-64">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-500"></div>
                <p class="ml-4 text-xl text-gray-600">Loading Dashboard Data...</p>
            </div>

            <main id="dashboard-content" class="hidden grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-1 bg-gradient-to-br from-blue-500 to-blue-700 text-white p-6 rounded-lg shadow-md flex flex-col justify-between transform transition-transform duration-300 hover:scale-[1.01]">
                    <h2 class="text-2xl font-semibold mb-4">Real-time Status</h2>
                    <div id="robot-status" class="text-3xl font-bold mb-2">Idle</div>
                    <p id="last-updated" class="text-sm opacity-80">Last updated: ...</p>
                    <div class="mt-4 text-xs">
                        <span class="inline-block bg-blue-800 px-3 py-1 rounded-full text-white">System Online</span>
                    </div>
                </div>

                <div class="lg:col-span-2 bg-gray-50 p-6 rounded-lg shadow-md transform transition-transform duration-300 hover:scale-[1.01]">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Live Inventory Tracking</h2>
                    <div class="overflow-x-auto max-h-96 custom-scrollbar mb-4">
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                            <thead class="bg-gray-100 sticky top-0">
                                <tr>
                                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider border-b">SKU</th>
                                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider border-b">Product Name</th>
                                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider border-b">Stock</th>
                                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider border-b">Min. Threshold</th>
                                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider border-b">Last Update</th>
                                </tr>
                            </thead>
                            <tbody id="inventory-table-body">
                            </tbody>
                        </table>
                    </div>
                    <p class="mt-4 text-sm text-gray-600">Inventory updates in real-time based on robot actions and camera scans.</p>
                    <button id="generate-suggestion-btn" class="mt-4 px-6 py-3 bg-indigo-600 text-white font-bold rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-300 ease-in-out flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                        ✨ Get Smart Reorder Suggestion ✨
                    </button>
                    <div id="reorder-suggestion-container" class="hidden mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg text-blue-800 text-sm">
                        <h3 class="font-semibold mb-2">Reorder Suggestion:</h3>
                        <pre id="reorder-suggestion-text" class="whitespace-pre-wrap font-sans"></pre>
                    </div>
                </div>

                <div class="lg:col-span-3 bg-gray-50 p-6 rounded-lg shadow-md transform transition-transform duration-300 hover:scale-[1.01]">
                <h2 class="mb-4 text-2xl font-semibold text-gray-800" style="text-align: center;">📋 Warehouse RFID Logs</h2>


                    <div class="mb-4 flex flex-wrap gap-3 items-end">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">From Date:</label>
                            <input type="date" id="minDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 form-control">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">To Date:</label>
                            <input type="date" id="maxDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 form-control">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 rfid-chart-canvas">
                        <div>
                            <canvas id="statusChart"></canvas>
                        </div>
                        <div>
                            <canvas id="itemChart"></canvas>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="rfidTable" class="table table-striped table-bordered w-full">
                            <thead class="bg-gray-700 text-white">
                            <tr>
                                <th class="py-3 px-4 text-left text-sm font-medium uppercase tracking-wider">ID</th>
                                <th class="py-3 px-4 text-left text-sm font-medium uppercase tracking-wider">Date</th>
                                <th class="py-3 px-4 text-left text-sm font-medium uppercase tracking-wider">Time</th>
                                <th class="py-3 px-4 text-left text-sm font-medium uppercase tracking-wider">RFID</th>
                                <th class="py-3 px-4 text-left text-sm font-medium uppercase tracking-wider">Status</th>
                                <th class="py-3 px-4 text-left text-sm font-medium uppercase tracking-wider">Item</th>
                                <th class="py-3 px-4 text-left text-sm font-medium uppercase tracking-wider">Size</th>
                                <th class="py-3 px-4 text-left text-sm font-medium uppercase tracking-wider">MFG</th>
                                <th class="py-3 px-4 text-left text-sm font-medium uppercase tracking-wider">EXP</th>
                            </tr>
                            </thead>
                            <tfoot class="bg-gray-100">
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>RFID</th>
                                <th></th>
                                <th>Item</th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            </tfoot>
                            <tbody id="rfidBody">
                                <?php
                                require_once 'config.php';
                                $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                                if ($conn->connect_error) {
                                    die("DB connection failed");
                                }
                                $sql = "SELECT * FROM rfid_logs ORDER BY id DESC";
                                $result = $conn->query($sql);
                                $statusData = [];
                                $itemData = [];
                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $dateParts = explode('-', $row['date']);
                                        $monthKey = $dateParts[0] . '-' . $dateParts[1];
                                        $status = $row['status'];
                                        $item = $row['item'];
                                        $statusData[$monthKey][$status] = ($statusData[$monthKey][$status] ?? 0) + 1;
                                        $itemData[$monthKey][$item] = ($itemData[$monthKey][$item] ?? 0) + 1;
                                        echo "<tr>
                                            <td class='py-2 px-4 text-sm text-gray-700'>{$row['id']}</td>
                                            <td class='py-2 px-4 text-sm text-gray-700'>{$row['date']}</td>
                                            <td class='py-2 px-4 text-sm text-gray-700'>{$row['time']}</td>
                                            <td class='py-2 px-4 text-sm text-gray-700'>{$row['rfid']}</td>
                                            <td class='py-2 px-4 text-sm text-gray-700'>{$row['status']}</td>
                                            <td class='py-2 px-4 text-sm text-gray-700'>{$row['item']}</td>
                                            <td class='py-2 px-4 text-sm text-gray-700'>{$row['size']}</td>
                                            <td class='py-2 px-4 text-sm text-gray-700'>{$row['mfg']}</td>
                                            <td class='py-2 px-4 text-sm text-gray-700'>{$row['exp']}</td>
                                        </tr>";
                                    }
                                }
                                $conn->close();
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="lg:col-span-3 bg-gray-50 p-6 rounded-lg shadow-md transform transition-transform duration-300 hover:scale-[1.01]">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Real-Time AI Demand Forecast</h2>
                     <p class="text-lg text-gray-600 mb-4">
                        Our advanced dashboard provides real-time, AI-powered demand forecasting. Click the button below to access live predictions and interactive analytics.
                    </p>
                    <button onclick="window.location.href='http://localhost:3000/'"
                        class="px-6 py-3 bg-purple-600 text-white font-bold rounded-lg shadow-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-300 ease-in-out flex items-center justify-center">
                        View Real-Time AI Forecast
                    </button>
                </div>

                <div class="lg:col-span-3 bg-gray-50 p-6 rounded-lg shadow-md transform transition-transform duration-300 hover:scale-[1.01]">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Bonus Features</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-gray-700">
                        <div class="flex flex-col items-center p-3 bg-white rounded-lg shadow-sm border border-gray-200">
                            <span class="text-blue-500 text-2xl mb-2">&#128226;</span>
                            <h3 class="font-semibold text-md mb-2">Smart Alerts</h3>
                            <p class="text-center text-sm mb-3">Get real-time notifications for low stock, anomalies.</p>
                            <label for="alerts-toggle" class="flex items-center cursor-pointer mb-2">
                                <div class="relative">
                                    <input type="checkbox" id="alerts-toggle" class="sr-only" />
                                    <div class="block bg-gray-600 w-12 h-6 rounded-full"></div>
                                    <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition transform duration-300"></div>
                                </div>
                                <div id="alerts-toggle-label" class="ml-3 text-gray-700 font-medium">Alerts Inactive</div>
                            </label>
                            <div id="alerts-container" class="hidden mt-2 p-3 bg-red-100 border border-red-300 rounded-lg text-red-800 text-xs w-full max-h-24 overflow-y-auto">
                                <h4 class="font-bold mb-1">Current Alerts:</h4>
                                <div id="alerts-list"></div>
                            </div>
                        </div>

                        <div class="flex flex-col items-center p-3 bg-white rounded-lg shadow-sm border border-gray-200">
                            <span class="text-blue-500 text-2xl mb-2">&#128227;</span>
                            <h3 class="font-semibold text-md mb-2">Auto Reordering Logic</h3>
                            <p class="text-center text-sm mb-3">Automated purchase orders based on thresholds & forecast.</p>
                           <button id="auto-reorder-btn"
                             class="px-4 py-2 bg-teal-600 text-white text-xs font-bold rounded-lg shadow-md hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition-all duration-300"
                            onclick="window.location.href='http://localhost/reorder.html'">
                            Simulate Auto-Reorder
                           </button>

                        </div>


                           <div class="flex flex-col items-center p-3 bg-white rounded-lg shadow-sm border border-gray-200">
                             <span class="text-blue-500 text-2xl mb-2">&#128187;</span>
                             <h3 class="font-semibold text-md mb-2">Supplier Integration</h3>
                             <p class="text-center text-sm mb-3">Connects with supplier APIs for seamless orders.</p>
                             <button id="supplier-integration-btn" class="px-4 py-2 bg-orange-600 text-white text-xs font-bold rounded-lg shadow-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-300"
                            onclick="window.location.href='file:///C:/Apache24/htdocs/reorder.html'">
                            Simulate Integration
                            </button>
                         </div>
                    </div>
                </div>
            </main>

            <footer class="mt-8 text-center text-gray-500 text-sm">
                &copy; <span id="footer-year"></span> Smart Warehouse Automation. All rights reserved.
            </footer>
        </div>
    </div>

    <script>
        // Wait for the DOM to be fully loaded before running scripts
        document.addEventListener('DOMContentLoaded', () => {

            // --- STATE MANAGEMENT ---
            // Data stores, similar to React state
            let inventory = [];
            let areAlertsActive = false;
            let isGeneratingSuggestion = false;

            // --- DOM ELEMENT REFERENCES ---
            const loader = document.getElementById('loader');
            const dashboardContent = document.getElementById('dashboard-content');
            const robotStatusEl = document.getElementById('robot-status');
            const lastUpdatedEl = document.getElementById('last-updated');
            const inventoryTableBody = document.getElementById('inventory-table-body');
            const generateSuggestionBtn = document.getElementById('generate-suggestion-btn');
            const reorderSuggestionContainer = document.getElementById('reorder-suggestion-container');
            const reorderSuggestionText = document.getElementById('reorder-suggestion-text');
            const alertsToggle = document.getElementById('alerts-toggle');
            const alertsToggleLabel = document.getElementById('alerts-toggle-label');
            const alertsContainer = document.getElementById('alerts-container');
            const alertsList = document.getElementById('alerts-list');
            const autoReorderBtn = document.getElementById('auto-reorder-btn');
            const supplierIntegrationBtn = document.getElementById('supplier-integration-btn');
            const footerYear = document.getElementById('footer-year');

            // --- INITIALIZATION ---
            // Set footer year and fetch initial data
            footerYear.textContent = new Date().getFullYear();
            fetchData();


            // --- DATA FETCHING & SIMULATION ---

            /**
             * Simulates fetching initial data from a backend.
             */
            async function fetchData() {
                // Show loader while fetching
                loader.style.display = 'flex';
                dashboardContent.style.display = 'none';

                // Simulate network delay
                await new Promise(resolve => setTimeout(resolve, 1500));

                // Mock Inventory Data
                inventory = [
                    { id: 'SKU001', name: 'Product A', stock: 150, location: 'A1-01', lastUpdate: '2025-05-31 10:00', minThreshold: 100 },
                    { id: 'SKU002', name: 'Product B', stock: 230, location: 'B2-05', lastUpdate: '2025-05-31 10:15', minThreshold: 180 },
                    { id: 'SKU003', name: 'Product C', stock: 80, location: 'C3-10', lastUpdate: '2025-05-31 10:30', minThreshold: 90 },
                    { id: 'SKU004', name: 'Product D', stock: 300, location: 'D4-02', lastUpdate: '2025-05-31 10:45', minThreshold: 250 },
                    { id: 'SKU005', name: 'Product E', stock: 120, location: 'E5-07', lastUpdate: '2025-05-31 11:00', minThreshold: 130 },
                ];

                // Hide loader and show content
                loader.style.display = 'none';
                dashboardContent.style.display = 'grid';

                // Render all components
                renderInventoryTable();
                updateAlerts();
                startRobotSimulation();

                // Initialize RFID Table and Charts after dashboard content is visible
                initializeRfidLogs(); 
            }

            /**
             * Simulates real-time robot status updates.
             */
            function startRobotSimulation() {
                const statusUpdates = [
                    'Robot Status: Navigating to A1-01', 'Robot Status: Arm picking SKU003',
                    'Robot Status: Delivering to Drop Zone', 'Robot Status: Arm placing SKU003',
                    'Robot Status: Camera logging action', 'Robot Status: Idle'
                ];
                let i = 0;
                setInterval(() => {
                    robotStatusEl.textContent = statusUpdates[i % statusUpdates.length];
                    lastUpdatedEl.textContent = `Last updated: ${new Date().toLocaleTimeString()}`;
                    i++;
                }, 3000);
            }

            // --- RENDERING FUNCTIONS ---

            /**
             * Renders the inventory table based on the current inventory state.
             */
            function renderInventoryTable() {
                inventoryTableBody.innerHTML = ''; // Clear previous content
                inventory.forEach(item => {
                    const row = document.createElement('tr');
                    row.className = 'hover:bg-gray-50 transition-colors duration-150 ease-in-out border-b last:border-b-0';
                    const stockColor = item.stock < item.minThreshold ? 'text-red-600' : 'text-green-600';

                    row.innerHTML = `
                        <td class="py-3 px-4 text-sm text-gray-700">${item.id}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">${item.name}</td>
                        <td class="py-3 px-4 text-sm font-semibold ${stockColor}">${item.stock}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">${item.minThreshold}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">${item.lastUpdate}</td>
                    `;
                    inventoryTableBody.appendChild(row);
                });
            }

            /**
             * Updates the alerts display based on inventory and toggle state.
             */
            function updateAlerts() {
                if (!areAlertsActive) {
                    alertsContainer.style.display = 'none';
                    return;
                }

                const currentAlerts = inventory
                    .filter(item => item.stock < item.minThreshold)
                    .map(item => ({
                        id: `alert-${item.id}`,
                        message: `SKU: ${item.id} (${item.name}) is below threshold (${item.stock} < ${item.minThreshold}).`,
                        timestamp: new Date().toLocaleTimeString()
                    }));

                if (currentAlerts.length > 0) {
                    alertsList.innerHTML = '';
                    currentAlerts.forEach(alert => {
                        const p = document.createElement('p');
                        p.className = 'mb-1';
                        p.textContent = `Low Stock: ${alert.message} (${alert.timestamp})`;
                        alertsList.appendChild(p);
                    });
                    alertsContainer.style.display = 'block';
                } else {
                    alertsContainer.style.display = 'none';
                }
            }

            // --- API & LLM INTEGRATION ---

            /**
             * Generates a reorder suggestion using the Gemini API.
             */
            async function handleGenerateSuggestion() {
                if (isGeneratingSuggestion) return;
                isGeneratingSuggestion = true;
                
                // Update button state to show loading
                generateSuggestionBtn.disabled = true;
                generateSuggestionBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Generating...`;
                reorderSuggestionContainer.style.display = 'block';
                reorderSuggestionText.textContent = 'Generating smart reorder suggestions...';

                try {
                    let prompt = "As a smart warehouse AI, provide reorder suggestions based on the following inventory data. Focus on items below their minimum threshold.\n\n";
                    prompt += "Current Inventory:\n" + inventory.map(item => `- Product: ${item.name} (SKU: ${item.id}), Stock: ${item.stock}, Min Threshold: ${item.minThreshold}`).join('\n');
                    prompt += "\n\nSuggest specific SKUs to reorder, recommended quantities, and a brief reason. Format as a bulleted list.";

                    const payload = { contents: [{ role: "user", parts: [{ text: prompt }] }] };
                    // IMPORTANT: Replace with your actual API key
                    const apiKey = "AIzaSyD0Sk6rviSoEiHgPqarhPSitNTVoNS3zRk"; // Provided by the Canvas environment
                    const apiUrl = `https://generativelanguage.googleapis.com/v1beta/models/gemini-1.0-pro:generateContent?key=${apiKey}`;

                    if (apiKey === "YOUR_API_KEY") {
                            reorderSuggestionText.textContent = "Error: API Key not configured. Please add your Gemini API key in the script.js file.";
                            return;
                    }

                    const response = await fetch(apiUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                    
                    if (!response.ok) {
                        const errorBody = await response.json();
                        throw new Error(`API Error: ${response.status} - ${errorBody.error.message}`);
                    }

                    const result = await response.json();

                    if (result.candidates && result.candidates.length > 0) {
                        reorderSuggestionText.textContent = result.candidates[0].content.parts[0].text;
                    } else {
                        reorderSuggestionText.textContent = "Could not generate a suggestion. The model returned no candidates. Please check your prompt and API settings.";
                    }
                } catch (error) {
                    console.error("Error during API call:", error);
                    reorderSuggestionText.textContent = "Error generating suggestion: " + error.message;
                } finally {
                    isGeneratingSuggestion = false;
                    generateSuggestionBtn.disabled = false;
                    generateSuggestionBtn.innerHTML = '✨ Get Smart Reorder Suggestion ✨';
                }
            }

            // --- EVENT LISTENERS ---
            generateSuggestionBtn.addEventListener('click', handleGenerateSuggestion);

            alertsToggle.addEventListener('change', (e) => {
                areAlertsActive = e.target.checked;
                alertsToggleLabel.textContent = areAlertsActive ? 'Alerts Active' : 'Alerts Inactive';
                updateAlerts();
            });

            

            supplierIntegrationBtn.addEventListener('click', () => {
                alert("Connecting to supplier systems via API/email to check stock and place orders. (Simulation)");
            });
        });

        // RFID Log Viewer Specific JavaScript (Integrated and Modified)
        let rfidTable; // Renamed to avoid conflict
        let statusChart;
        let itemChart;

        function initializeRfidLogs() {
            rfidTable = $('#rfidTable').DataTable({
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                initComplete: function () {
                    this.api().columns([3, 5]).every(function () {
                        var column = this;
                        var input = $('<input type="text" class="form-control form-control-sm" placeholder="Search">')
                            .appendTo($(column.footer()).empty())
                            .on('keyup change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            });
                    });
                }
            });

            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                var min = $('#minDate').val();
                var max = $('#maxDate').val();
                var date = data[1]; // Assuming date is in the second column (index 1)
                return (!min && !max) || (!min && date <= max) || (min <= date && !max) || (min <= date && date <= max);
            });

            $('#minDate, #maxDate').on('change', function () {
                rfidTable.draw(); // Use rfidTable instead of table
            });

            function generateChartData(data, labelType) {
                const labels = Object.keys(data);
                const datasets = [];
                const colors = ['#0d6efd','#198754','#ffc107','#dc3545','#6610f2','#20c997']; // Bootstrap colors for consistency
                const allKeys = new Set();
                labels.forEach(month => {
                    Object.keys(data[month]).forEach(k => allKeys.add(k));
                });
                Array.from(allKeys).forEach((key, i) => {
                    datasets.push({
                        label: key,
                        backgroundColor: colors[i % colors.length],
                        data: labels.map(month => data[month][key] || 0)
                    });
                });
                return { labels, datasets };
            }

            // PHP variables directly accessible since they are rendered in the HTML
            const statusChartData = <?php echo json_encode($statusData ?? []); ?>;
            const itemChartData = <?php echo json_encode($itemData ?? []); ?>;

            const ctx1 = document.getElementById('statusChart').getContext('2d');
            const ctx2 = document.getElementById('itemChart').getContext('2d');

            statusChart = new Chart(ctx1, {
                type: 'bar',
                data: generateChartData(statusChartData, 'Status'),
                options: {
                    responsive: true,
                    animation: false, // Keep animation off as requested
                    plugins: { title: { display: true, text: 'Received / Dispatched per Month' } }
                }
            });

            itemChart = new Chart(ctx2, {
                type: 'bar',
                data: generateChartData(itemChartData, 'Items'),
                options: {
                    responsive: true,
                    animation: false, // Keep animation off as requested
                    plugins: { title: { display: true, text: 'Items Received / Dispatched per Month' } }
                }
            });
        }

    </script>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

</body>
</html>
