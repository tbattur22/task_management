import { useEffect } from 'react';
import { ToastContainer, toast } from 'react-toastify';
import Task from '@/components/tasks/task';
import { type SharedData, ProjectType, TaskType } from '@/types';
import { Head, Link, usePage, router } from '@inertiajs/react';

type TasksProps = {
    projects: ProjectType[],
    selectedProject: ProjectType,
    tasks: TaskType[]
}

export default function Tasks({projects, selectedProject, tasks} : TasksProps) {
    const { auth, flash } = usePage<SharedData>().props;
    console.log(`Tasks():projects:selectedProject:tasks`,projects,selectedProject,tasks);
    // const allProject = {id:0, 'name': 'All Projects'};
    // const allProjects = [allProject, ...projects];

    console.log(`Tasks():selectedProject`,selectedProject);
    // const projects = ['All Projects', 'Project Alpha', 'Project Beta', 'Project Gamma'];

    const setSelectedProject = (projectId:string) => {
        console.log(`setSelectedProject():projectId`,projectId);
        router.post(route('project.select',projectId));
    }
    const handleCreate = () => {
        console.log(`handleCreate()`);
        router.post(route('task.create'));
    };

    const handleEdit = (taskToEdit: TaskType) => {
        console.log(`handleEdit():taskToEdit`,taskToEdit);
        const updatedName = prompt('Edit task name:', taskToEdit.name);
        router.get(route('task.edit',taskToEdit.id));
    };

      const handleDelete = (id:number) => {
        if (window.confirm('Are you sure you want to delete this task?')) {
            router.delete(route('task.destroy', id));
        //   setTasks(tasks.filter(task => task.id !== id));
        }
      };

    //   const filteredTasks =
    // selectedProject === 'All Projects'
    //   ? tasks
    //   : tasks.filter(task => task.project === selectedProject);

    useEffect(() => {
        if (flash?.message) {
            toast(flash.message);
        }
    }, [flash]);
    if (!selectedProject) return null;

    return (
        <div className="min-h-screen bg-gray-50 px-6">
            <div className="max-w-3xl mx-auto">
                <h1 className="m-3 p-6 text-2xl font-bold text-gray-800 text-center">Task Manager</h1>
                <div className="flex justify-between items-center mb-6">
                    <div className="flex gap-3 items-center">
                        <select
                            value={selectedProject.id}
                            onChange={(e) => setSelectedProject(e.target.value)}
                            className="border border-gray-300 rounded px-3 py-2 text-sm"
                            >
                            {projects?.map((proj) => (
                                <option key={proj.id} value={proj.id}>
                                {proj.name}
                                </option>
                            ))}
                        </select>
                        <button
                            onClick={handleCreate}
                            className="bg-green-500 text-white px-4 py-2 rounded-2xl hover:bg-green-600"
                        >
                            Add Task
                        </button>
                    </div>
                </div>
                <div className="space-y-4">
                    {tasks?.length > 0 ? (
                        tasks?.map(task => (
                        <Task
                            key={task.id}
                            task={task}
                            onEdit={handleEdit}
                            onDelete={handleDelete}
                        />
                        ))
                    ) : (
                        <p className="text-gray-500 text-center">No tasks in this project.</p>
                    )}
                </div>
            </div>
            <ToastContainer
                position="top-right"
                autoClose={2000}
                hideProgressBar={false}
                newestOnTop={false}
                closeOnClick={false}
                rtl={false}
                pauseOnFocusLoss
                draggable
                pauseOnHover
                theme="light"
            />
        </div>
    )
}
