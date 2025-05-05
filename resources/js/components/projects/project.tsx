import React from 'react'
import { ProjectType } from '@/types';

const Project = ({project}: {project: ProjectType}) => {
    console.log(`Project():`,project);
    const {id, name} = project;

  return (
    <div>
        <div className="grid grid-cols-[var(--project-cols)] gap-4 items-center border-b border-gray-200 py-2">
            <div>{id}</div>
            <div>{name}</div>
            <div>
                <button className="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">Edit</button>
            </div>
            <div>
                <button className="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">Delete</button>
            </div>
        </div>
    </div>
  )
}

export default Project
