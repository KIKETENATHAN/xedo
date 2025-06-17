import React, { useState } from 'react';
import Modal from './Modal';

const DriversTable = () => {
    const [drivers, setDrivers] = useState([]);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [newDriver, setNewDriver] = useState({ name: '', license: '' });

    const handleAddDriver = () => {
        setDrivers([...drivers, newDriver]);
        setNewDriver({ name: '', license: '' });
        setIsModalOpen(false);
    };

    return (
        <div className="p-4">
            <h2 className="text-xl font-semibold mb-4">Drivers</h2>
            <button 
                className="mb-4 px-4 py-2 bg-blue-500 text-white rounded" 
                onClick={() => setIsModalOpen(true)}
            >
                Add Driver
            </button>
            <table className="min-w-full bg-white border border-gray-300">
                <thead>
                    <tr>
                        <th className="py-2 px-4 border-b">Name</th>
                        <th className="py-2 px-4 border-b">License</th>
                    </tr>
                </thead>
                <tbody>
                    {drivers.map((driver, index) => (
                        <tr key={index}>
                            <td className="py-2 px-4 border-b">{driver.name}</td>
                            <td className="py-2 px-4 border-b">{driver.license}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
            {isModalOpen && (
                <Modal onClose={() => setIsModalOpen(false)}>
                    <h2 className="text-lg font-semibold mb-4">Add New Driver</h2>
                    <input 
                        type="text" 
                        placeholder="Driver Name" 
                        value={newDriver.name} 
                        onChange={(e) => setNewDriver({ ...newDriver, name: e.target.value })} 
                        className="border p-2 mb-4 w-full"
                    />
                    <input 
                        type="text" 
                        placeholder="Driver License" 
                        value={newDriver.license} 
                        onChange={(e) => setNewDriver({ ...newDriver, license: e.target.value })} 
                        className="border p-2 mb-4 w-full"
                    />
                    <button 
                        className="px-4 py-2 bg-green-500 text-white rounded" 
                        onClick={handleAddDriver}
                    >
                        Add Driver
                    </button>
                </Modal>
            )}
        </div>
    );
};

export default DriversTable;