import React from 'react';

const VehiclesTable: React.FC = () => {
    const vehicles = [
        { id: 1, model: 'Toyota Corolla', year: 2020, licensePlate: 'ABC123' },
        { id: 2, model: 'Honda Civic', year: 2019, licensePlate: 'XYZ456' },
        { id: 3, model: 'Ford Focus', year: 2021, licensePlate: 'LMN789' },
    ];

    return (
        <div className="overflow-x-auto">
            <table className="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr className="w-full bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th className="py-3 px-6 text-left">ID</th>
                        <th className="py-3 px-6 text-left">Model</th>
                        <th className="py-3 px-6 text-left">Year</th>
                        <th className="py-3 px-6 text-left">License Plate</th>
                    </tr>
                </thead>
                <tbody className="text-gray-600 text-sm font-light">
                    {vehicles.map(vehicle => (
                        <tr key={vehicle.id} className="border-b border-gray-200 hover:bg-gray-100">
                            <td className="py-3 px-6 text-left">{vehicle.id}</td>
                            <td className="py-3 px-6 text-left">{vehicle.model}</td>
                            <td className="py-3 px-6 text-left">{vehicle.year}</td>
                            <td className="py-3 px-6 text-left">{vehicle.licensePlate}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
};

export default VehiclesTable;