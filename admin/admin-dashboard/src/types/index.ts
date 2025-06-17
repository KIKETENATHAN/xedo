export interface Driver {
    id: number;
    name: string;
    licenseNumber: string;
    phoneNumber: string;
    email: string;
    status: 'active' | 'inactive';
}

export interface Passenger {
    id: number;
    name: string;
    phoneNumber: string;
    email: string;
    registeredAt: Date;
}

export interface Sacco {
    id: number;
    name: string;
    location: string;
    contactNumber: string;
}

export interface Vehicle {
    id: number;
    plateNumber: string;
    model: string;
    year: number;
    driverId: number;
}

export interface Transaction {
    id: number;
    amount: number;
    date: Date;
    type: 'credit' | 'debit';
    description: string;
}

export interface Trip {
    id: number;
    vehicleId: number;
    driverId: number;
    passengerId: number;
    startLocation: string;
    endLocation: string;
    tripDate: Date;
    fare: number;
}