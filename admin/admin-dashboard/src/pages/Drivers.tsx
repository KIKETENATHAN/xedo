import React, { useState } from 'react';
import DriversTable from '../components/DriversTable';
import Modal from '../components/Modal';

const Drivers = () => {
    const [isModalOpen, setIsModalOpen] = useState(false);

    const handleOpenModal = () => {
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
    };

    return (
        <div className="p-4">
            <h1 className="text-2xl font-bold mb-4">Drivers</h1>
            <button 
                onClick={handleOpenModal} 
                className="mb-4 px-4 py-2 bg-blue-500 text-white rounded"
            >
                Add Driver
            </button>
            <DriversTable />
            {isModalOpen && (
                <Modal onClose={handleCloseModal}>
                    {/* Add Driver Form goes here */}
                    <h2 className="text-xl mb-4">Add New Driver</h2>
                    {/* Form fields for adding a driver */}
                    <form>
                        <div className="mb-4">
                            <label className="block mb-2">Name</label>
                            <input type="text" className="border rounded w-full p-2" />
                        </div>
                        <div className="mb-4">
                            <label className="block mb-2">License Number</label>
                            <input type="text" className="border rounded w-full p-2" />
                        </div>
                        <div className="mb-4">
                            <label className="block mb-2">Phone Number</label>
                            <input type="text" className="border rounded w-full p-2" />
                        </div>
                        <button 
                            type="submit" 
                            className="px-4 py-2 bg-green-500 text-white rounded"
                        >
                            Add Driver
                        </button>
                    </form>
                </Modal>
            )}
        </div>
    );
};

export default Drivers;