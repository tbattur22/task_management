import { useState, ChangeEvent, FormEvent } from 'react'
import { router, usePage } from '@inertiajs/react'
import { type BreadcrumbItem, ProjectType } from '@/types';
import AppLayout from '@/layouts/app-layout';

export default function CreateEdit({projectToEdit}:{projectToEdit:ProjectType}) {
    const {errors} = usePage().props;

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: '/dashboard',
        },
        {
            title: 'Projects',
            href: '/projects',
        },
        {
          title: (projectToEdit ? 'Edit Project' : 'Create Project'),
          href: '/projects',
      },
    ];

  let projectData = {
    name: "",
    created_at: new Date().toISOString(),
    updated_at: new Date().toISOString()
  }

  if (projectToEdit) {
    projectData = projectToEdit;
  }

  const [values, setValues] = useState(projectData);

  function handleChange(e: ChangeEvent<HTMLInputElement>) {
    const key = e.target.id;
    const value = e.target.value;
    console.log(`handleChange():key:${key} and value:${value}`);
    setValues(values => ({
        ...values,
        [key]: value,
    }))
  }

  function handleSubmit(e: FormEvent<HTMLFormElement>) {
    e.preventDefault();
    if (projectToEdit) {// edit project
        console.log(`handleSubmit():editing existing project:making put request, values`,values);
        router.put(`/projects/${projectToEdit.id}`, values)
    } else {// new project
        console.log(`handleSubmit():creating new project, values`,values);
        router.post('/projects',values);
    }
  }

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
    <form className='flex items-center justify-center' onSubmit={handleSubmit}>
        <div className='w-2xl flex flex-col m-4 p-4 gap-4'>
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
