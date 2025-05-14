import {useState, useEffect} from 'react';
import { type SharedData, ProjectType, TaskType } from '@/types';
import { Head, Link, usePage, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import Project from '@/pages/projects/project';
import Tasks from '../tasks/tasks';

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
  const [projectsData, setProjectsData] = useState(projects);
  const handleCreate = () => {
      router.post(route('project.create'));
  };

  const handleEdit = (projectToEdit: ProjectType) => {
      router.get(route('project.edit',projectToEdit.id));
  };

  const handleDelete = (id: number) => {
    const foundProject = projectsData.find(project => project.id === id);
    if (!foundProject) throw new Error(`The project with id ${id} not found!`);

    if (window.confirm(`Are you sure you want to delete the project: ${foundProject.name}?`)) {
        router.delete(route('project.destroy', id));
    }
  };

  useEffect(() => {
    setProjectsData(projects);
  }, [projects]);

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
        <div className='m-4 p-4'>
            <h2 className='m-4 p-4 font-bold text-center text-blue-900'>Projects</h2>
            <div className="flex justify-end m-2 p-2">
                <button onClick={handleCreate} className="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
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
                {projectsData.map((proj, i) => {
                    return <Project key={proj.id} project={proj} onEdit={handleEdit} onDelete={handleDelete} />
                })}
            </div>
        </div>
    </AppLayout>
  )
}

export default Projects;
