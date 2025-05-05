import { useState } from 'react'
import { router, usePage } from '@inertiajs/react'
import { ProjectType, TaskType } from '@/types';

export default function TaskCreateEdit({projects, taskToEdit}:{projects: ProjectType[], taskToEdit:TaskType}) {
    console.log(`Task:Create/Edit():projects:task`,projects,taskToEdit);
    const {errors} = usePage().props;
    console.log(`TaskCreateEdit:errors`,errors);
  let taskData = {
    name: "",
    priority: 1,
    project_id: (projects?.length ? projects[0].id : 0),
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
        router.post('/tasks',values);
    }
  }

  return (
    <form className='flex items-center justify-center' onSubmit={handleSubmit}>
        <div className='flex flex-col m-4 p-4 gap-4'>
            <div className='flex items-center'>
                <div className='w-1/3'>
                    <label className='block text-center text-gray-500 font-bold' htmlFor="project_id">Projects</label>
                </div>
                <div className='w-2/3'>
                    <div className="inline-block relative w-64">
                        <select
                            id="project_id"
                            value={values.project_id}
                            onChange={handleChange}
                            className="block appearance-none bg-white border border-gray-400 rounded px-4 py-2 pr-8 shadow focus:outline-none"
                            >
                            {projects.map((proj) => (
                                <option key={proj.id} value={proj.id}>
                                    {proj.name}
                                </option>
                            ))}
                        </select>
                        <div className="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg className="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                        </div>
                    </div>
                </div>
            </div>
            <div className='flex items-center'>
                <div className='w-1/3'>
                    <label className='block text-center text-gray-500 font-bold' htmlFor="name">Name</label>
                </div>
                <div className='w-2/3'>
                    <input className='appearance-none w-full leading-tight bg-gray-200 text-gray-700 py-3 px-2 border border-gray-200 rounded focus:outline-none focus:bg-white focus:border-gray-500' id="name" value={values.name} onChange={handleChange} />
                    {errors?.name && <p className='text-red-500'>{errors.name}</p>}
                </div>
            </div>
            <div className='flex items-center'>
                <div className='w-1/3'>
                    <label className='block text-center text-gray-500 font-bold' htmlFor="priority">Priority</label>
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
  )
}
