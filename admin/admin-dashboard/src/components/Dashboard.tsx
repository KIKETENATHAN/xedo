import React, { useState } from 'react';
import Aside from './Aside';
import Card from './Card';
import Modal from './Modal';

const Dashboard = () => {
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [modalContent, setModalContent] = useState(null);

    const openModal = (content) => {
        setModalContent(content);
        setIsModalOpen(true);
    };

    const closeModal = () => {
        setIsModalOpen(false);
        setModalContent(null);
    };

    return (
        <div className="flex">
            <Aside openModal={openModal} />
            <div className="flex-1 p-6">
                <h1 className="text-2xl font-bold mb-4">Admin Dashboard</h1>
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <Card title="Drivers" count={/* fetch drivers count */} onClick={() => openModal('drivers')} />
                    <Card title="Passengers" count={/* fetch passengers count */} onClick={() => openModal('passengers')} />
                    <Card title="Saccos" count={/* fetch saccos count */} onClick={() => openModal('saccos')} />
                    <Card title="Vehicles" count={/* fetch vehicles count */} onClick={() => openModal('vehicles')} />
                    <Card title="Transactions" count={/* fetch transactions count */} onClick={() => openModal('transactions')} />
                    <Card title="Trips" count={/* fetch trips count */} onClick={() => openModal('trips')} />
                </div>
                {isModalOpen && (
                    <Modal onClose={closeModal}>
                        {modalContent === 'drivers' && <DriversTable />}
                        {modalContent === 'passengers' && <PassengersTable />}
                        {modalContent === 'saccos' && <SaccosTable />}
                        {modalContent === 'vehicles' && <VehiclesTable />}
                        {modalContent === 'transactions' && <TransactionsTable />}
                        {modalContent === 'trips' && <TripsTable />}
                    </Modal>
                )}
            </div>
        </div>
    );
};

export default Dashboard;