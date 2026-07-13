import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import { 
    LayoutDashboard, User, Calendar, Bed, Pill, 
    Activity, ClipboardList, Wallet, Star, FileText, 
    Users, Settings, LogOut, HeartPulse 
} from 'lucide-react';

export default function Sidebar() {
    const { auth } = usePage().props;
    const user = auth?.user || { name: 'Guest', role: 'patient' };

    const getNavigationLinks = (role) => {
        const links = [];

        // Patient links
        if (role === 'patient') {
            links.push(
                { name: 'Dashboard', href: '/patient/dashboard', icon: LayoutDashboard },
                { name: 'My Appointments', href: '/patient/appointments', icon: Calendar },
                { name: 'Book Appointment', href: '/patient/book', icon: Calendar },
                { name: 'Medical History', href: '/patient/history', icon: ClipboardList },
                { name: 'My Bills', href: '/patient/bills', icon: Wallet }
            );
        }

        // Doctor links
        if (role === 'doctor') {
            links.push(
                { name: 'Doctor Dashboard', href: '/doctor/dashboard', icon: LayoutDashboard },
                { name: 'Patient History', href: '/admin/patients', icon: ClipboardList },
                { name: 'Doctor Reviews', href: '/doctor/reviews', icon: Star }
            );
        }

        // Nurse links
        if (role === 'nurse') {
            links.push(
                { name: 'Nurse Dashboard', href: '/nurse/dashboard', icon: LayoutDashboard },
                { name: 'Bed Occupancy', href: '/admin/beds', icon: Bed }
            );
        }

        // Receptionist links
        if (role === 'receptionist') {
            links.push(
                { name: 'Receptionist Desk', href: '/admin/patients', icon: Users },
                { name: 'Book Appointment', href: '/patient/book', icon: Calendar },
                { name: 'Bed Allocations', href: '/admin/beds', icon: Bed }
            );
        }

        // Cashier links
        if (role === 'cashier') {
            links.push(
                { name: 'Billing Desk', href: '/admin/billing', icon: Wallet }
            );
        }

        // Admin links (Super / Hospital Admin)
        if (role === 'super_admin' || role === 'hospital_admin') {
            links.push(
                { name: 'Admin Dashboard', href: '/admin/dashboard', icon: LayoutDashboard },
                { name: 'Staff Management', href: '/admin/users', icon: Users },
                { name: 'Patient Directory', href: '/admin/patients', icon: ClipboardList },
                { name: 'Bed Management', href: '/admin/beds', icon: Bed },
                { name: 'Surgery Scheduler', href: '/admin/surgeries', icon: Activity },
                { name: 'Inventory & Supplies', href: '/admin/inventory', icon: ClipboardList },
                { name: 'Billing & Payments', href: '/admin/billing', icon: Wallet },
                { name: 'Reports & Analytics', href: '/admin/reports', icon: FileText },
                { name: 'System Settings', href: '/admin/settings', icon: Settings }
            );
        }

        return links;
    };

    const links = getNavigationLinks(user.role);

    return (
        <aside className="fixed inset-y-0 left-0 z-20 flex w-64 flex-col bg-slate-900 text-white shadow-xl">
            {/* Logo brand */}
            <div className="flex h-16 items-center px-6 border-b border-slate-800">
                <HeartPulse className="h-8 w-8 text-blue-400 mr-3" />
                <span className="text-lg font-bold tracking-wider uppercase text-blue-400">Jude Health</span>
            </div>

            {/* Profile info */}
            <div className="flex items-center px-6 py-5 border-b border-slate-800">
                <div className="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center font-bold text-white uppercase shadow-sm">
                    {user.name.substring(0, 2)}
                </div>
                <div className="ml-4 overflow-hidden">
                    <h4 className="text-sm font-semibold truncate">{user.name}</h4>
                    <p className="text-xs text-blue-300 capitalize truncate">{user.role.replace('_', ' ')}</p>
                </div>
            </div>

            {/* Links */}
            <nav className="flex-1 space-y-1 px-4 py-6 overflow-y-auto">
                {links.map((link, idx) => {
                    const Icon = link.icon;
                    return (
                        <Link
                            key={idx}
                            href={link.href}
                            className="flex items-center px-4 py-3 text-sm font-medium rounded-md text-slate-300 hover:bg-slate-800 hover:text-white transition-colors"
                        >
                            <Icon className="h-5 w-5 mr-3 text-slate-400 group-hover:text-white" />
                            {link.name}
                        </Link>
                    );
                })}
            </nav>

            {/* Logout Footer */}
            <div className="p-4 border-t border-slate-800">
                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    className="flex w-full items-center px-4 py-3 text-sm font-medium text-rose-400 hover:bg-rose-950/40 rounded-md transition-colors"
                >
                    <LogOut className="h-5 w-5 mr-3 text-rose-400" />
                    Sign Out
                </Link>
            </div>
        </aside>
    );
}
