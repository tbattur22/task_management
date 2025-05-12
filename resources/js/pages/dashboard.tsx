import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

export default function Dashboard() {
  return (
    <AppLayout breadcrumbs={breadcrumbs}>
        <div className='m-2 p-2'>
            <Head title="Dashboard" />
            <h1 className="mb-8 text-3xl text-center font-bold">Dashboard</h1>
            <p className="mb-8 text-center leading-normal">Dashboard for Task Management Demo App</p>
        </div>
    </AppLayout>
  );
}
