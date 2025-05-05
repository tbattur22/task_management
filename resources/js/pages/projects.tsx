import { type SharedData, ProjectType, TaskType } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import Project from '@/components/projects/project';
import Tasks from '../components/tasks/tasks';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Projects',
        href: '/projects',
    },
];

const Projects = ({projects}: {projects:ProjectType[]}) => {
    const { auth } = usePage<SharedData>().props;
    console.log(`Projects()`,projects);

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
        <div className='m-4 p-4'>
            <h2 className='m-4 p-4 font-bold text-center text-blue-900'>Projects</h2>
            <div className="flex justify-end m-2 p-2">
                <button className="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                    Create
                </button>
            </div>
            <div className="grid grid-cols-[var(--project-cols)] gap-4 font-semibold text-gray-700 border-b border-gray-300 pb-2 mb-2">
                <div>ID</div>
                <div>Name</div>
                <div>Edit</div>
                <div>Delete</div>
            </div>

            <div className='flex flex-col'>
                {projects.map((proj, i) => {
                    return <Project key={proj.id} project={proj} />
                })}
            </div>
        </div>
    </AppLayout>
  )
}

export default Projects;
