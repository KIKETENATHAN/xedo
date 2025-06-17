import React, { useState } from 'react';
import TripsTable from '../components/TripsTable';
import Modal from '../components/Modal';

const Trips = () => {
    const [isModalOpen, setIsModalOpen] = useState(false);

    const handleOpenModal = () => {
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
    };

    return (
        <div className="p-4">
            <h1 className="text-2xl font-bold mb-4">Trips Management</h1>
            <button 
                onClick={handleOpenModal} 
                className="mb-4 px-4 py-2 bg-blue-500 text-white rounded"
            >
                Add New Trip
            </button>
            <TripsTable />
            {isModalOpen && (
                <Modal onClose={handleCloseModal}>
                    {/* Modal content for adding a new trip */}
                    <h2 className="text-lg font-semibold mb-4">Add New Trip</h2>
                    {/* Form for adding a trip goes here */}
                    <form>
                        <div className="mb-4">
                            <label className="block text-sm font-medium mb-2">Trip Name</label>
                            <input type="text" className="border rounded w-full p-2" />
                        </div>
                        <div className="mb-4">
                            <label className="block text-sm font-medium mb-2">Destination</label>
                            <input type="text" className="border rounded w-full p-2" />
                        </div>
                        <div className="mb-4">
                            <label className="block text-sm font-medium mb-2">Date</label>
                            <input type="date" className="border rounded w-full p-2" />
                        </div>
                        <button 
                            type="submit" 
                            className="px-4 py-2 bg-green-500 text-white rounded"
                        >
                            Save Trip
                        </button>
                    </form>
                </Modal>
            )}
        </div>
    );
};

export default Trips;