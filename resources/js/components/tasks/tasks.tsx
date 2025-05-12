import { useState, useEffect, useCallback } from 'react';
import { ToastContainer, toast } from 'react-toastify';
import axios from 'axios';
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
    const [tasksData, setTasksData] = useState(tasks);
    const [dragDropped, setDragDropped] = useState(false);

    const setSelectedProject = (projectId:string) => {
        console.log(`setSelectedProject():projectId`,projectId);
        router.post(route('project.select',projectId));
    }
    const handleCreate = () => {
        console.log(`handleCreate()`);
        router.post(route('task.create',selectedProject.id));
    };

    const handleEdit = (taskToEdit: TaskType) => {
        console.log(`handleEdit():taskToEdit`,taskToEdit);
        const updatedName = prompt('Edit task name:', taskToEdit.name);
        router.get(route('task.edit',taskToEdit.id));
    };

    const handleDelete = (id:number) => {
      if (window.confirm('Are you sure you want to delete this task?')) {
          router.delete(route('task.destroy', id));
      }
    };

    const moveTask = (from:number, to:number) => {
      console.log(`moveTask():from ${from} to ${to}`);
      const updatedTasksData = [...tasksData];
      const [movedTask] = updatedTasksData.splice(from, 1);
      updatedTasksData.splice(to, 0, movedTask);

      // change the tasks' priorities on the client side first
      updatedTasksData.forEach((obj, i) => {
        obj.priority = i + 1;
      });

      console.log(`moveTask():updatedTasks`,updatedTasksData);
      setTasksData(updatedTasksData);
      setDragDropped(true);
    };

    //reload the home page as failed to sync priority changes
    const reloadAfterSyncFailure = (error = null) => {
      if (error) console.log(error);
      alert(`Oops! something went wrong, could not save the changes.`);
      router.get(route('home'));
    }

    useEffect(() => {
      setTasksData(tasks);
    }, [tasks]);

    // if task order changed via drag and drop sync the changed priorities in the backend
    useEffect(() => {
      if (dragDropped) {
        axios.post('/priority', tasksData.map((el) => el.id).join())
        .then(function (res) {
          if (res.status !== 200 || res?.data?.status !== 'success') {
            reloadAfterSyncFailure(res?.data?.data);
          }
        })
        .catch(function (error) {
          reloadAfterSyncFailure(error);
        });

        setDragDropped(false);
      }
   }, [dragDropped]);

    // if any flash message returned from server display it
    useEffect(() => {
        if (flash?.message) {
            toast(flash.message);
        }
    }, [flash]);

    if (!selectedProject) return null;

    console.log(`Tasks(): before render. tasksData`,tasksData);
    return (
        <div className="min-h-screen bg-gray-50 px-6">
            <div className="max-w-3xl mx-auto">
                <h1 className="m-3 p-6 text-2xl font-bold text-gray-800 text-center">Task Manager</h1>
                <div className="flex justify-between items-center mb-6">
                    {projects?.length > 0 && selectedProject && (
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
                    )}
                </div>
                <div className="space-y-4">
                    {tasksData?.length > 0 ? (
                        tasksData?.map((task, ind) => (
                        <Task
                            key={task.id}
                            index={ind}
                            task={task}
                            onEdit={handleEdit}
                            onDelete={handleDelete}
                            moveTask={moveTask}
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
