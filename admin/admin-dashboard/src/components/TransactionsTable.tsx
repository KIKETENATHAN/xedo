import React from 'react';

const TransactionsTable = () => {
    const transactions = [
        // Sample data structure for transactions
        { id: 1, amount: 100, date: '2023-01-01', driver: 'John Doe', passenger: 'Jane Smith' },
        { id: 2, amount: 150, date: '2023-01-02', driver: 'Alice Johnson', passenger: 'Bob Brown' },
        // Add more sample transactions as needed
    ];

    return (
        <div className="overflow-x-auto">
            <table className="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr className="w-full bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th className="py-3 px-6 text-left">Transaction ID</th>
                        <th className="py-3 px-6 text-left">Amount</th>
                        <th className="py-3 px-6 text-left">Date</th>
                        <th className="py-3 px-6 text-left">Driver</th>
                        <th className="py-3 px-6 text-left">Passenger</th>
                    </tr>
                </thead>
                <tbody className="text-gray-600 text-sm font-light">
                    {transactions.map(transaction => (
                        <tr key={transaction.id} className="border-b border-gray-200 hover:bg-gray-100">
                            <td className="py-3 px-6">{transaction.id}</td>
                            <td className="py-3 px-6">${transaction.amount}</td>
                            <td className="py-3 px-6">{transaction.date}</td>
                            <td className="py-3 px-6">{transaction.driver}</td>
                            <td className="py-3 px-6">{transaction.passenger}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
};

export default TransactionsTable;