import React, { useState } from 'react';
import VehiclesTable from '../components/VehiclesTable';
import Modal from '../components/Modal';

const Vehicles: React.FC = () => {
    const [isModalOpen, setIsModalOpen] = useState(false);

    const handleOpenModal = () => {
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
    };

    return (
        <div className="p-4">
            <h1 className="text-2xl font-bold mb-4">Vehicles Management</h1>
            <button 
                onClick={handleOpenModal} 
                className="mb-4 px-4 py-2 bg-blue-500 text-white rounded"
            >
                Add Vehicle
            </button>
            <VehiclesTable />
            {isModalOpen && (
                <Modal onClose={handleCloseModal}>
                    {/* Add Vehicle Form goes here */}
                    <h2 className="text-xl mb-4">Add New Vehicle</h2>
                    {/* Form fields for adding a vehicle */}
                    <form>
                        <div className="mb-4">
                            <label className="block mb-2">Vehicle Name</label>
                            <input type="text" className="border rounded w-full p-2" />
                        </div>
                        <div className="mb-4">
                            <label className="block mb-2">Vehicle Type</label>
                            <input type="text" className="border rounded w-full p-2" />
                        </div>
                        <div className="mb-4">
                            <label className="block mb-2">License Plate</label>
                            <input type="text" className="border rounded w-full p-2" />
                        </div>
                        <button 
                            type="submit" 
                            className="px-4 py-2 bg-green-500 text-white rounded"
                        >
                            Submit
                        </button>
                    </form>
                </Modal>
            )}
        </div>
    );
};

export default Vehicles;