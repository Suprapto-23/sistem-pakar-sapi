<style>
    :root {
        --primary: #10b981;
        --primary-dark: #059669;
        --secondary: #3b82f6;
        --accent: #f59e0b;
    }
    
    body {
        font-family: 'Inter', sans-serif;
    }
    
    .diagnosa-gradient {
        background: linear-gradient(135deg, #f0fdf4 0%, #f0f9ff 100%);
    }
    
    .card-hover {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .card-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    .progress-bar {
        transition: width 0.5s ease-in-out;
    }
    
    .fade-in {
        opacity: 0;
        transform: translateY(10px);
        transition: all 0.5s ease-out;
    }
    
    .fade-in.visible {
        opacity: 1;
        transform: translateY(0);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
    }
    
    .confidence-high { background: linear-gradient(135deg, #10b981, #059669); }
    .confidence-medium { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .confidence-low { background: linear-gradient(135deg, #ef4444, #dc2626); }
    
    .print-only { display: none; }
    
    @media print {
        .no-print { display: none !important; }
        .print-only { display: block !important; }
        .break-before { page-break-before: always; }
        .break-after { page-break-after: always; }
        .break-inside { page-break-inside: avoid; }
        
        body {
            background: white !important;
            font-size: 12pt;
        }
        
        .bg-gradient-to-br { background: white !important; }
        .shadow-lg { box-shadow: none !important; }
        .border { border: 1px solid #e5e7eb !important; }
    }
    
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>