import React, { useEffect, useState } from 'react';
import { Head } from '@inertiajs/react';
import Sidebar from '../../Components/Sidebar';
import Header from '../../Components/Header';
import Card from '../../Components/Card';
import Table from '../../Components/Table';
import Badge from '../../Components/Badge';
import Button from '../../Components/Button';
import Modal from '../../Components/Modal';
import Input from '../../Components/Input';
import Toast from '../../Components/Toast';
import { AlertTriangle, Plus, PackageOpen } from 'lucide-react';

export default function InventoryManagement() {
    const [supplies, setSupplies] = useState([]);
    const [restockModalOpen, setRestockModalOpen] = useState(false);
    const [selectedSupply, setSelectedSupply] = useState(null);
    const [quantity, setQuantity] = useState(50);
    const [toast, setToast] = useState(null);

    useEffect(() => {
        setSupplies([
            { id: 1, supply_code: 'SUP-01', supply_name: 'Nylon Suture 3-0', category: 'suture', current_stock: 45, reorder_level: 15, purchase_price: 30.00, selling_price: 45.00 },
            { id: 2, supply_code: 'SUP-02', supply_name: 'Sterile Gauze Pads 4x4', category: 'dressing', current_stock: 120, reorder_level: 30, purchase_price: 5.00, selling_price: 8.50 },
            { id: 3, supply_code: 'SUP-03', supply_name: 'Surgical Gloves Size 7.5', category: 'glove', current_stock: 80, reorder_level: 25, purchase_price: 25.00, selling_price: 40.00 },
            { id: 4, supply_code: 'SUP-06', supply_name: 'Foley Catheter 16 Fr', category: 'catheter', current_stock: 9, reorder_level: 10, purchase_price: 8.00, selling_price: 15.00 }, // Low Stock
            { id: 5, supply_code: 'SUP-08', supply_name: 'Titanium Hip Implant', category: 'implant', current_stock: 4, reorder_level: 3, purchase_price: 1200.00, selling_price: 2500.00 }
        ]);
    }, []);

    const openRestockModal = (sup) => {
        setSelectedSupply(sup);
        setQuantity(50);
        setRestockModalOpen(true);
    };

    const handleRestockSubmit = () => {
        setSupplies(supplies.map(s => s.id === selectedSupply.id ? { ...s, current_stock: s.current_stock + parseInt(quantity) } : s));
        setToast({ type: 'success', message: `Stock level for ${selectedSupply.supply_name} incremented by ${quantity}!` });
        setRestockModalOpen(false);
    };

    return (
        <div className="min-h-screen bg-gray-50 pl-64">
            <Head title="Surgical Supply Inventory" />
            <Sidebar />

            <div className="flex flex-col min-h-screen">
                <Header title="Surgical Supply Inventory" />

                <main className="flex-1 p-8 space-y-8 max-w-7xl mx-auto w-full">
                    {/* Low stock indicators */}
                    <div className="space-y-4">
                        <h3 className="text-base font-bold text-gray-900 flex items-center">
                            <AlertTriangle className="h-5 w-5 text-amber-500 mr-2" /> Stock Reorder Warnings
                        </h3>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {supplies.filter(s => s.current_stock <= s.reorder_level).map((sup) => (
                                <div key={sup.id} className="p-4 bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-lg flex items-center justify-between font-medium">
                                    <div className="flex items-center">
                                        <AlertTriangle className="h-5 w-5 text-amber-500 mr-3 flex-shrink-0" />
                                        <span>{sup.supply_name} ({sup.supply_code}) is below reorder level: Only {sup.current_stock} remaining!</span>
                                    </div>
                                    <Button size="sm" variant="outline" className="border-amber-300 text-amber-800 hover:bg-amber-100 shadow-none" onClick={() => openRestockModal(sup)}>
                                        Restock
                                    </Button>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Master supplies listing table */}
                    <Card>
                        <Table headers={['Supply Code', 'Item Description', 'Category', 'Current Stock', 'Min/Max Stock', 'Prices (Buy/Sell)', 'Actions']}>
                            {supplies.map((sup) => {
                                const isLow = sup.current_stock <= sup.reorder_level;
                                return (
                                    <tr key={sup.id} className="hover:bg-gray-50">
                                        <td className="px-6 py-4 font-semibold text-gray-900">{sup.supply_code}</td>
                                        <td className="px-6 py-4 font-bold text-gray-900">{sup.supply_name}</td>
                                        <td className="px-6 py-4 capitalize text-gray-500">{sup.category}</td>
                                        <td className="px-6 py-4">
                                            <span className={`font-bold ${isLow ? 'text-rose-600' : 'text-slate-800'}`}>
                                                {sup.current_stock}
                                            </span>
                                            {isLow && <Badge type="error" className="ml-2">Low</Badge>}
                                        </td>
                                        <td className="px-6 py-4 text-xs text-gray-500">{sup.reorder_level} / 100</td>
                                        <td className="px-6 py-4 text-sm font-medium">
                                            <span className="text-gray-400">Buy:</span> ${sup.purchase_price.toFixed(2)} &bull; <span className="text-gray-400">Sell:</span> <span className="text-blue-600 font-bold">${sup.selling_price.toFixed(2)}</span>
                                        </td>
                                        <td className="px-6 py-4">
                                            <Button size="sm" variant="primary" onClick={() => openRestockModal(sup)}>
                                                Restock
                                            </Button>
                                        </td>
                                    </tr>
                                );
                            })}
                        </Table>
                    </Card>
                </main>
            </div>

            {/* Restock form modal */}
            <Modal
                isOpen={restockModalOpen}
                title={`Procure Supplies Restock: ${selectedSupply?.supply_name}`}
                onClose={() => setRestockModalOpen(false)}
                onConfirm={handleRestockSubmit}
                confirmText="Fulfill Restock & Add Stock"
                confirmVariant="success"
            >
                <div className="space-y-4">
                    <div className="bg-gray-50 p-4 rounded-lg flex justify-between items-center text-sm border border-gray-100">
                        <span className="text-gray-500">Unit Price Cost</span>
                        <span className="font-bold text-gray-800">${selectedSupply?.purchase_price.toFixed(2)} / unit</span>
                    </div>

                    <Input
                        label="Restock Quantity"
                        name="qty"
                        type="number"
                        min="1"
                        value={quantity}
                        onChange={(e) => setQuantity(e.target.value)}
                        required
                    />

                    <div className="text-right text-xs text-gray-500">
                        Total estimated procurement cost: <span className="font-bold text-gray-800">${(selectedSupply?.purchase_price * quantity || 0).toFixed(2)}</span>
                    </div>
                </div>
            </Modal>

            {toast && (
                <Toast type={toast.type} message={toast.message} onClose={() => setToast(null)} />
            )}
        </div>
    );
}
