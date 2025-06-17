import React, { useState } from 'react';
import Modal from './Modal';

const TripsTable = () => {
    const [trips, setTrips] = useState([]);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [newTrip, setNewTrip] = useState({ destination: '', date: '', driverId: '' });

    const handleAddTrip = () => {
        setTrips([...trips, newTrip]);
        setNewTrip({ destination: '', date: '', driverId: '' });
        setIsModalOpen(false);
    };

    return (
        <div className="p-4">
            <h2 className="text-xl font-bold mb-4">Trips</h2>
            <button 
                className="mb-4 px-4 py-2 bg-blue-500 text-white rounded" 
                onClick={() => setIsModalOpen(true)}
            >
                Add Trip
            </button>
            <table className="min-w-full bg-white border border-gray-300">
                <thead>
                    <tr>
                        <th className="py-2 px-4 border-b">Destination</th>
                        <th className="py-2 px-4 border-b">Date</th>
                        <th className="py-2 px-4 border-b">Driver ID</th>
                    </tr>
                </thead>
                <tbody>
                    {trips.map((trip, index) => (
                        <tr key={index}>
                            <td className="py-2 px-4 border-b">{trip.destination}</td>
                            <td className="py-2 px-4 border-b">{trip.date}</td>
                            <td className="py-2 px-4 border-b">{trip.driverId}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
            {isModalOpen && (
                <Modal onClose={() => setIsModalOpen(false)}>
                    <h2 className="text-lg font-bold mb-4">Add New Trip</h2>
                    <div className="mb-4">
                        <label className="block mb-2">Destination</label>
                        <input 
                            type="text" 
                            value={newTrip.destination} 
                            onChange={(e) => setNewTrip({ ...newTrip, destination: e.target.value })} 
                            className="border rounded w-full py-2 px-3"
                        />
                    </div>
                    <div className="mb-4">
                        <label className="block mb-2">Date</label>
                        <input 
                            type="date" 
                            value={newTrip.date} 
                            onChange={(e) => setNewTrip({ ...newTrip, date: e.target.value })} 
                            className="border rounded w-full py-2 px-3"
                        />
                    </div>
                    <div className="mb-4">
                        <label className="block mb-2">Driver ID</label>
                        <input 
                            type="text" 
                            value={newTrip.driverId} 
                            onChange={(e) => setNewTrip({ ...newTrip, driverId: e.target.value })} 
                            className="border rounded w-full py-2 px-3"
                        />
                    </div>
                    <button 
                        className="px-4 py-2 bg-green-500 text-white rounded" 
                        onClick={handleAddTrip}
                    >
                        Add Trip
                    </button>
                </Modal>
            )}
        </div>
    );
};

export default TripsTable;