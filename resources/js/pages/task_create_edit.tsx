import { useState } from 'react'
import { router, usePage } from '@inertiajs/react'
import { ProjectType, TaskType, type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/app-layout';

export default function TaskCreateEdit({project, taskToEdit}:{project: ProjectType, taskToEdit:TaskType}) {
    console.log(`Task:Create/Edit():project:task`,project,taskToEdit);
    const {errors} = usePage().props;
    console.log(`TaskCreateEdit:errors`,errors);

    const breadcrumbs: BreadcrumbItem[] = [
      {
          title: 'Dashboard',
          href: '/dashboard',
      },
      {
          title: 'Task Management',
          href: '/',
      },
      {
        title: (taskToEdit ? 'Edit Task' : 'Create Task'),
        href: '/',
    },
  ];

  let taskData = {
    name: "",
    priority: 1,
    project_id: (project.id),
    created_at: new Date().toISOString(),
    updated_at: new Date().toISOString()
  }
  if (taskToEdit) {
    taskData = taskToEdit;
  }

  const [values, setValues] = useState(taskData);

  function handleChange(e) {
    const key = e.target.id;
    const value = e.target.value;
    console.log(`handleChange():key:${key} and value:${value}`);
    setValues(values => ({
        ...values,
        [key]: value,
    }))
  }

  function handleSubmit(e) {
    e.preventDefault();
    if (taskToEdit) {// edit task
        console.log(`handleSubmit():editing existing task:making put request, values`,values);
        router.put(`/tasks/${taskToEdit.id}`, values)
    } else {// new task
        console.log(`handleSubmit():creating new task, values`,values);
        router.post(route('task.store'),values);
        // router.post('/test-inertia', {
        //   some: 'data',
        // });
    }
  }

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
    <form className='flex items-center justify-center' onSubmit={handleSubmit}>
        <div className='flex flex-col m-4 p-4 gap-4'>
            <div className='flex items-center'>
                <div className='w-1/3'>
                    <label className='block text-center text-gray-500 font-bold' htmlFor="project_name">Project name</label>
                </div>
                <div className='w-2/3'>
                    <div id="project_name" className="inline-block relative w-64 text-black font-bold">
                      {project.name}
                    </div>
                </div>
            </div>
            <div className='flex items-center'>
                <div className='w-1/3'>
                    <label className='block text-center text-gray-500 font-bold' htmlFor="name">Task name</label>
                </div>
                <div className='w-2/3'>
                    <input className='appearance-none w-full leading-tight bg-gray-200 text-gray-700 py-3 px-2 border border-gray-200 rounded focus:outline-none focus:bg-white focus:border-gray-500' id="name" value={values.name} onChange={handleChange} />
                    {errors?.name && <p className='text-red-500'>{errors.name}</p>}
                </div>
            </div>
            <div className='flex items-center'>
                <div className='w-1/3'>
                    <label className='block text-center text-gray-500 font-bold' htmlFor="priority">Task priority</label>
                </div>
                <div className='w-2/3'>
                    <input className='appearance-none w-full leading-tight bg-gray-200 text-gray-700 py-3 px-4 border border-gray-200 rounded focus:outline-none focus:bg-white focus:border-gray-500' id="priority" value={values.priority} onChange={handleChange} />
                    {errors?.priority && <p className='text-red-500'>{errors.priority}</p>}
                </div>
            </div>
            <div className='flex items-center'>
                <div className='w-1/3'></div>
                <div className='w-2/3'>
                    <button className="shadow bg-indigo-500 hover:bg-indigo-700 focus:shadow-outline focus:outline-none text-white font-bold py-2 px-4 rounded" type="submit">Submit</button>
                </div>
            </div>
        </div>
    </form>
    </AppLayout>
  )
}
