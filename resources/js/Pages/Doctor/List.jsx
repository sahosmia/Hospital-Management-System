import React, { useEffect, useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import Sidebar from '../../Components/Sidebar';
import Header from '../../Components/Header';
import Card from '../../Components/Card';
import Badge from '../../Components/Badge';
import Input from '../../Components/Input';
import { Stethoscope, Star, ArrowRight } from 'lucide-react';

export default function DoctorList() {
    const [doctors, setDoctors] = useState([]);
    const [specialtyFilter, setSpecialtyFilter] = useState('');

    useEffect(() => {
        setDoctors([
            { id: 3, name: 'Dr. Elizabeth Blackwell', specialty: 'Cardiology', experience: 12, rating: 4.8 },
            { id: 4, name: 'Dr. Robert Koch', specialty: 'Neurology', experience: 15, rating: 4.9 },
            { id: 5, name: 'Dr. Virginia Apgar', specialty: 'Pediatrics', experience: 8, rating: 4.7 }
        ]);
    }, []);

    const filteredDocs = doctors.filter(doc => 
        !specialtyFilter || doc.specialty.toLowerCase().includes(specialtyFilter.toLowerCase())
    );

    return (
        <div className="min-h-screen bg-gray-50 pl-64">
            <Head title="Find a Specialist" />
            <Sidebar />

            <div className="flex flex-col min-h-screen">
                <Header title="Find a Specialist" />

                <main className="flex-1 p-8 space-y-8 max-w-7xl mx-auto w-full">
                    {/* Search filter bar */}
                    <div className="bg-white p-6 rounded-lg border border-gray-200 shadow-sm flex items-center justify-between">
                        <div className="w-full max-w-md">
                            <Input
                                placeholder="Filter by Specialty (e.g. Cardiology, Neurology)"
                                name="specialty"
                                value={specialtyFilter}
                                onChange={(e) => setSpecialtyFilter(e.target.value)}
                                className="mb-0"
                            />
                        </div>
                        <Badge type="info">Active Rosters</Badge>
                    </div>

                    {/* Doctors grid */}
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {filteredDocs.map((doc) => (
                            <Card key={doc.id} className="p-6 space-y-4 hover:shadow-md transition-shadow">
                                <div className="flex items-center space-x-4">
                                    <div className="h-14 w-14 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-2xl uppercase">
                                        {doc.name.replace('Dr. ', '').substring(0, 1)}
                                    </div>
                                    <div>
                                        <h4 className="font-bold text-gray-900 text-base">{doc.name}</h4>
                                        <p className="text-sm text-gray-500 font-medium">{doc.specialty}</p>
                                    </div>
                                </div>

                                <div className="pt-3 border-t border-gray-100 flex justify-between items-center text-sm">
                                    <span className="text-gray-400">Experience</span>
                                    <span className="font-semibold text-gray-800">{doc.experience} Years</span>
                                </div>

                                <div className="flex justify-between items-center text-sm">
                                    <span className="text-gray-400">Rating</span>
                                    <span className="font-semibold text-amber-500 flex items-center">
                                        <Star className="h-4 w-4 fill-current mr-1" /> {doc.rating}
                                    </span>
                                </div>

                                <Link href={`/doctor/profile?id=${doc.id}`} className="block pt-3">
                                    <button className="w-full bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold py-2.5 rounded transition-colors flex items-center justify-center">
                                        View Schedules & Reviews <ArrowRight className="h-3.5 w-3.5 ml-2" />
                                    </button>
                                </Link>
                            </Card>
                        ))}
                    </div>
                </main>
            </div>
        </div>
    );
}
