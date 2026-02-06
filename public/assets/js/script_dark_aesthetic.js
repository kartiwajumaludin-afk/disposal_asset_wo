ocument.addEventListener('DOMContentLoaded', function() {
    console.log('DRAMATIC ASSET Dashboard loaded.');
    
    // ===== DATA SAMPLE =====
    const sampleData = [
        { id: 1, ticket: 'TICK-10001', site: 'Jakarta Central', asset: 'ASSET-3456', status: 'pending', region: 'Regional A', startDate: '2024-03-15', lastUpdate: '2024-03-20' },
        { id: 2, ticket: 'TICK-10002', site: 'Surabaya East', asset: 'ASSET-7890', status: 'completed', region: 'Regional B', startDate: '2024-03-10', lastUpdate: '2024-03-18' },
        { id: 3, ticket: 'TICK-10003', site: 'Bandung West', asset: 'ASSET-1234', status: 'active', region: 'Regional C', startDate: '2024-03-12', lastUpdate: '2024-03-19' },
        { id: 4, ticket: 'TICK-10004', site: 'Medan North', asset: 'ASSET-5678', status: 'pending', region: 'Regional A', startDate: '2024-03-14', lastUpdate: '2024-03-21' },
        { id: 5, ticket: 'TICK-10005', site: 'Makassar South', asset: 'ASSET-9012', status: 'completed', region: 'Regional B', startDate: '2024-03-08', lastUpdate: '2024-03-16' }
    ];
    
    // ===== 1. SIDEBAR ICON INTERACTIONS =====
    const navIcons = document.querySelectorAll('.nav-icon');
    
    navIcons.forEach(icon => {
        icon.addEventListener('click', function() {
            navIcons.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            
            const menuType = this.getAttribute('data-menu');
            updateMainMenuTitle(menuType);
            
            console.log(`Switched to ${menuType}`);
        });
    });
    
    function updateMainMenuTitle(menuType) {
        const mainMenuTitle = document.getElementById('mainMenuTitle');
        const titles = {
            'main': 'Dismantle Asset Write-Off',
            'dashboard': 'Dashboard Overview',
            'boq': 'BoQ Calculation',
            'tracker': 'Tracker Inbound'
        };
        
        mainMenuTitle.textContent = titles[menuType] || titles['main'];
    }
    
    // ===== 2. SUBMENU INTERACTIONS =====
    const submenuTabs = document.querySelectorAll('.submenu-tab');
    
    submenuTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            submenuTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            const tabColor = this.style.getPropertyValue('--tab-color');
            document.getElementById('mainMenuTitle').style.setProperty('--active-submenu-color', tabColor);
            
            const submenuType = this.getAttribute('data-submenu');
            updateTableForSubmenu(submenuType);
            updateTableTitle(submenuType);
            
            console.log(`Showing ${this.querySelector('span').textContent} data`);
        });
    });
    
    function updateTableForSubmenu(submenuType) {
        const tableBody = document.getElementById('table-body');
        tableBody.innerHTML = '';
        
        sampleData.forEach(item => {
            const row = document.createElement('tr');
            
            let statusClass = '', statusText = '';
            switch(item.status) {
                case 'pending': statusClass = 'status-pending'; statusText = 'Pending'; break;
                case 'completed': statusClass = 'status-completed'; statusText = 'Completed'; break;
                case 'active': statusClass = 'status-active'; statusText = 'Active'; break;
            }
            
            row.innerHTML = `
                <td>${item.id}</td>
                <td><strong>${item.ticket}</strong></td>
                <td>${item.site}</td>
                <td>${item.asset}</td>
                <td><span class="status-cell ${statusClass}">${statusText}</span></td>
                <td>${item.region}</td>
                <td>${formatDate(item.startDate)}</td>
                <td>${formatDate(item.lastUpdate)}</td>
                <td>
                    <div class="action-cell">
                        <button class="table-action-btn view"><i class="fas fa-eye"></i></button>
                        <button class="table-action-btn edit"><i class="fas fa-edit"></i></button>
                        <button class="table-action-btn delete"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            `;
            
            tableBody.appendChild(row);
        });
        
        addTableActionListeners();
    }
    
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    }
    
    function updateTableTitle(submenuType) {
        const tableTitle = document.querySelector('.table-title h2');
        const tableSubtitle = document.querySelector('.table-subtitle');
        
        const titles = {
            'tracker': { main: 'Tracker Records', subtitle: 'Showing asset tracking data', icon: 'fa-satellite-dish' },
            'asset': { main: 'Asset Database', subtitle: 'Showing asset inventory data', icon: 'fa-box' },
            'workinfo': { main: 'Work Information', subtitle: 'Showing work status and details', icon: 'fa-clipboard-list' },
            'daily': { main: 'Daily Activity', subtitle: 'Showing daily operations log', icon: 'fa-running' },
            'import': { main: 'Import Console', subtitle: 'Showing CSV import operations', icon: 'fa-file-import' },
            'procedures': { main: 'Procedures Log', subtitle: 'Showing system procedures execution', icon: 'fa-play-circle' }
        };
        
        const titleData = titles[submenuType] || titles['tracker'];
        tableTitle.innerHTML = `<i class="fas ${titleData.icon}"></i> ${titleData.main}`;
        tableSubtitle.textContent = titleData.subtitle;
    }
    
    // ===== 3. TABLE ACTION LISTENERS =====
    function addTableActionListeners() {
        document.querySelectorAll('.table-action-btn.view').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const ticket = row.cells[1].textContent;
                console.log(`Viewing details for ${ticket}`);
            });
        });
        
        document.querySelectorAll('.table-action-btn.edit').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const ticket = row.cells[1].textContent;
                console.log(`Editing record ${ticket}`);
            });
        });
        
        document.querySelectorAll('.table-action-btn.delete').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const ticket = row.cells[1].textContent;
                if (confirm(`Delete record ${ticket}?`)) {
                    row.remove();
                    console.log(`Record ${ticket} deleted`);
                }
            });
        });
    }
    
    // ===== 4. REGION FILTER =====
    const regionChips = document.querySelectorAll('.region-chip');
    
    regionChips.forEach(chip => {
        chip.addEventListener('click', function() {
            const region = this.getAttribute('data-region');
            
            if (region === 'all') {
                regionChips.forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
            } else {
                document.querySelector('[data-region="all"]').classList.remove('selected');
                this.classList.toggle('selected');
                
                const selectedCount = document.querySelectorAll('.region-chip.selected').length;
                if (selectedCount === 0) {
                    document.querySelector('[data-region="all"]').classList.add('selected');
                }
            }
            
            console.log(`Region filter: ${region}`);
        });
    });
    
    // ===== 5. FILTER DROPDOWNS =====
    const filterDropdowns = document.querySelectorAll('.filter-dropdown');
    
    filterDropdowns.forEach(dropdown => {
        dropdown.addEventListener('change', function() {
            const label = this.previousElementSibling.textContent;
            const value = this.options[this.selectedIndex].text;
            console.log(`${label} set to: ${value}`);
        });
    });
    
    // ===== 6. DATE RANGE =====
    const dateInputs = document.querySelectorAll('.date-input');
    
    dateInputs.forEach(input => {
        input.addEventListener('change', function() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            if (startDate && endDate) {
                console.log(`Date range: ${startDate} to ${endDate}`);
            }
        });
    });
    
    // ===== 7. SEARCH =====
    const globalSearch = document.getElementById('globalSearch');
    const clearSearchBtn = document.querySelector('.clear-search');
    
    let searchTimeout;
    globalSearch.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if (this.value.trim()) {
                console.log(`Searching for: "${this.value}"`);
            }
        }, 500);
    });
    
    clearSearchBtn.addEventListener('click', function() {
        globalSearch.value = '';
        console.log('Search cleared');
    });
    
    // ===== 8. FILTER ACTIONS =====
    const filterApplyBtn = document.querySelector('.filter-apply-btn');
    const filterClearBtn = document.querySelector('.filter-clear-btn');
    
    if (filterApplyBtn) {
        filterApplyBtn.addEventListener('click', function() {
            console.log('Filters applied');
        });
    }
    
    if (filterClearBtn) {
        filterClearBtn.addEventListener('click', function() {
            regionChips.forEach(chip => chip.classList.remove('selected'));
            document.querySelector('[data-region="all"]').classList.add('selected');
            filterDropdowns.forEach(dropdown => dropdown.value = 'all');
            dateInputs.forEach(input => input.value = '');
            globalSearch.value = '';
            console.log('All filters cleared');
        });
    }
    
    // ===== 9. LOGOUT =====
    const logoutBtn = document.querySelector('.logout-btn');
    
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to logout?')) {
                console.log('Logging out...');
                // window.location.href = '/logout';
            }
        });
    }
    
    // ===== 10. EXPORT =====
    const exportBtn = document.querySelector('.export-btn');
    
    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            console.log('Exporting data to CSV...');
        });
    }
    
    // ===== 11. PAGINATION =====
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    const pageNumbers = document.querySelectorAll('.page-number');
    const pageSizeSelect = document.getElementById('pageSize');
    
    if (prevBtn) prevBtn.addEventListener('click', () => console.log('Previous page'));
    if (nextBtn) nextBtn.addEventListener('click', () => console.log('Next page'));
    
    pageNumbers.forEach(page => {
        page.addEventListener('click', function() {
            pageNumbers.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            console.log(`Page ${this.textContent}`);
        });
    });
    
    if (pageSizeSelect) {
        pageSizeSelect.addEventListener('change', function() {
            console.log(`Rows per page: ${this.value}`);
        });
    }
    
    // ===== 12. TABLE SEARCH =====
    const tableSearch = document.querySelector('.search-box input');
    
    if (tableSearch) {
        tableSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#table-body tr');
            
            rows.forEach(row => {
                const rowText = row.textContent.toLowerCase();
                row.style.display = rowText.includes(searchTerm) ? '' : 'none';
            });
            
            if (searchTerm) console.log(`Table search: "${searchTerm}"`);
        });
    }
    
    // ===== 13. INITIALIZE =====
    function initializeDashboard() {
        document.querySelector('[data-region="all"]').classList.add('selected');
        updateTableForSubmenu('tracker');
        
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('endDate').value = today;
        
        const thirtyDaysAgo = new Date();
        thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
        document.getElementById('startDate').value = thirtyDaysAgo.toISOString().split('T')[0];
        
        console.log('Dashboard initialized');
    }
    
    // ===== 14. START =====
    initializeDashboard();
});