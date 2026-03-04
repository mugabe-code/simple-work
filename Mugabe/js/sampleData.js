const sampleSpareParts = [
    {
        id: 1,
        name: "Engine Oil Filter",
        category: "Engine",
        quantity: 25,
        unitPrice: 1500,
        totalPrice: 37500
    },
    {
        id: 2,
        name: "Brake Pads Set",
        category: "Brake System",
        quantity: 12,
        unitPrice: 8500,
        totalPrice: 102000
    },
    {
        id: 3,
        name: "Air Filter",
        category: "Engine",
        quantity: 18,
        unitPrice: 2200,
        totalPrice: 39600
    },
    {
        id: 4,
        name: "Spark Plugs (Set of 4)",
        category: "Engine",
        quantity: 8,
        unitPrice: 3500,
        totalPrice: 28000
    },
    {
        id: 5,
        name: "Battery",
        category: "Electrical",
        quantity: 5,
        unitPrice: 45000,
        totalPrice: 225000
    }
];

const sampleStockInRecords = [
    {
        id: 101,
        partId: 1,
        partName: "Engine Oil Filter",
        category: "Engine",
        quantity: 30,
        date: "2026-01-15",
        supplier: "Auto Parts Rwanda",
        notes: "Monthly stock replenishment"
    },
    {
        id: 102,
        partId: 2,
        partName: "Brake Pads Set",
        category: "Brake System",
        quantity: 15,
        date: "2026-01-20",
        supplier: "Brake Specialists Ltd",
        notes: "Emergency order"
    }
];

const sampleStockOutRecords = [
    {
        id: 201,
        partId: 1,
        partName: "Engine Oil Filter",
        category: "Engine",
        quantity: 5,
        unitPrice: 1500,
        totalPrice: 7500,
        date: "2026-01-18",
        customer: "Service Department",
        purpose: "Vehicle maintenance"
    },
    {
        id: 202,
        partId: 2,
        partName: "Brake Pads Set",
        category: "Brake System",
        quantity: 3,
        unitPrice: 8500,
        totalPrice: 25500,
        date: "2026-01-22",
        customer: "Repairs Division",
        purpose: "Customer vehicle repair"
    }
];

const sampleUsers = [
    {
        id: 1,
        fullName: "System Administrator",
        username: "admin",
        password: "admin123",
        role: "admin",
        email: "admin@smartpark.rw",
        phone: "+250780000000",
        createdDate: "2026-01-01",
        status: "active"
    }
];

function initializeSampleData() {
    if (!localStorage.getItem('spareParts') && !localStorage.getItem('users')) {
        localStorage.setItem('spareParts', JSON.stringify(sampleSpareParts));
        localStorage.setItem('stockInRecords', JSON.stringify(sampleStockInRecords));
        localStorage.setItem('stockOutRecords', JSON.stringify(sampleStockOutRecords));
        localStorage.setItem('users', JSON.stringify(sampleUsers));
        
        console.log('Sample data initialized successfully');
        alert('Sample data has been loaded into the system for demonstration purposes.');
    } else {
        console.log('Data already exists, not loading sample data');
    }
}

if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        sampleSpareParts,
        sampleStockInRecords,
        sampleStockOutRecords,
        sampleUsers,
        initializeSampleData
    };
}