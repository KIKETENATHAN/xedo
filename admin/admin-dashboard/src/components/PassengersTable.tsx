import React from 'react';

const PassengersTable = () => {
    const [passengers, setPassengers] = React.useState([]);
    const [loading, setLoading] = React.useState(true);

    React.useEffect(() => {
        // Fetch passengers data from API or state management
        const fetchPassengers = async () => {
            // Simulating an API call
            const response = await fetch('/api/passengers');
            const data = await response.json();
            setPassengers(data);
            setLoading(false);
        };

        fetchPassengers();
    }, []);

    if (loading) {
        return <div>Loading...</div>;
    }

    return (
        <div className="overflow-x-auto">
            <table className="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr className="w-full bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th className="py-3 px-6 text-left">Passenger ID</th>
                        <th className="py-3 px-6 text-left">Name</th>
                        <th className="py-3 px-6 text-left">Email</th>
                        <th className="py-3 px-6 text-left">Phone</th>
                        <th className="py-3 px-6 text-left">Created At</th>
                    </tr>
                </thead>
                <tbody className="text-gray-600 text-sm font-light">
                    {passengers.map((passenger) => (
                        <tr key={passenger.id} className="border-b border-gray-200 hover:bg-gray-100">
                            <td className="py-3 px-6">{passenger.id}</td>
                            <td className="py-3 px-6">{passenger.name}</td>
                            <td className="py-3 px-6">{passenger.email}</td>
                            <td className="py-3 px-6">{passenger.phone}</td>
                            <td className="py-3 px-6">{new Date(passenger.createdAt).toLocaleDateString()}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
};

export default PassengersTable;