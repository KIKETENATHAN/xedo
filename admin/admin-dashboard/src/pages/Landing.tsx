import React, { useState, useEffect } from 'react';
import Dashboard from '../components/Dashboard';
import Card from '../components/Card';

const Landing: React.FC = () => {
    const [summaryData, setSummaryData] = useState({
        drivers: 0,
        passengers: 0,
        saccos: 0,
        vehicles: 0,
        transactions: 0,
        trips: 0,
    });

    useEffect(() => {
        // Fetch summary data from API or database
        const fetchData = async () => {
            // Replace with actual API calls
            const data = {
                drivers: 10,
                passengers: 200,
                saccos: 5,
                vehicles: 15,
                transactions: 50,
                trips: 30,
            };
            setSummaryData(data);
        };

        fetchData();
    }, []);

    return (
        <Dashboard>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-4">
                <Card title="Drivers" count={summaryData.drivers} />
                <Card title="Passengers" count={summaryData.passengers} />
                <Card title="Saccos" count={summaryData.saccos} />
                <Card title="Vehicles" count={summaryData.vehicles} />
                <Card title="Transactions" count={summaryData.transactions} />
                <Card title="Trips" count={summaryData.trips} />
            </div>
        </Dashboard>
    );
};

export default Landing;