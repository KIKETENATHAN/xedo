import React from 'react';
import TransactionsTable from '../components/TransactionsTable';

const Transactions: React.FC = () => {
    return (
        <div className="p-4">
            <h1 className="text-2xl font-bold mb-4">Transactions</h1>
            <TransactionsTable />
        </div>
    );
};

export default Transactions;