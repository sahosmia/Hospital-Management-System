import React, { useEffect, useState } from 'react';
import { Head } from '@inertiajs/react';
import Sidebar from '../../Components/Sidebar';
import Header from '../../Components/Header';
import Card from '../../Components/Card';
import Badge from '../../Components/Badge';
import { ShieldAlert, Activity, ClipboardCheck } from 'lucide-react';

export default function MedicalHistory() {
    const [history, setHistory] = useState({ allergies: [], conditions: [], pastSurgeries: [] });

    useEffect(() => {
        setHistory({
            allergies: ['Penicillin Allergy'],
            conditions: ['Mild Asthma', 'Controlled Hypertension'],
            pastSurgeries: ['Appendectomy (2021)']
        });
    }, []);

    return (
        <div className="min-h-screen bg-gray-50 pl-64">
            <Head title="My Clinical History" />
            <Sidebar />

            <div className="flex flex-col min-h-screen">
                <Header title="My Clinical History" />

                <main className="flex-1 p-8 space-y-8 max-w-5xl mx-auto w-full">
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {/* Allergies Card */}
                        <Card className="p-6 space-y-4 border-t-4 border-rose-500">
                            <h3 className="text-md font-bold text-gray-900 flex items-center">
                                <ShieldAlert className="h-5 w-5 text-rose-500 mr-2" /> Medical Allergies
                            </h3>
                            <div className="space-y-2">
                                {history.allergies.map((all, idx) => (
                                    <div key={idx} className="p-2.5 bg-rose-50 border border-rose-100 rounded text-rose-800 text-xs font-semibold">
                                        {all}
                                    </div>
                                ))}
                            </div>
                        </Card>

                        {/* Chronic Conditions Card */}
                        <Card className="p-6 space-y-4 border-t-4 border-blue-500">
                            <h3 className="text-md font-bold text-gray-900 flex items-center">
                                <Activity className="h-5 w-5 text-blue-500 mr-2" /> Chronic Conditions
                            </h3>
                            <div className="space-y-2">
                                {history.conditions.map((cond, idx) => (
                                    <div key={idx} className="p-2.5 bg-blue-50 border border-blue-100 rounded text-blue-800 text-xs font-semibold">
                                        {cond}
                                    </div>
                                ))}
                            </div>
                        </Card>

                        {/* Past Surgeries Card */}
                        <Card className="p-6 space-y-4 border-t-4 border-indigo-500">
                            <h3 className="text-md font-bold text-gray-900 flex items-center">
                                <ClipboardCheck className="h-5 w-5 text-indigo-500 mr-2" /> Past Operations
                            </h3>
                            <div className="space-y-2">
                                {history.pastSurgeries.map((surg, idx) => (
                                    <div key={idx} className="p-2.5 bg-indigo-50 border border-indigo-100 rounded text-indigo-800 text-xs font-semibold">
                                        {surg}
                                    </div>
                                ))}
                            </div>
                        </Card>
                    </div>
                </main>
            </div>
        </div>
    );
}
