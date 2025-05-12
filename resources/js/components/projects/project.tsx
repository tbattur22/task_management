import React, {FC} from 'react'
import { ProjectType } from '@/types';

interface ProjectProps {
  project: ProjectType,
  onEdit: (item: ProjectType) => void,
  onDelete:(id: number) => void
}

const Project:FC<ProjectProps>  = ({project, onEdit, onDelete}) => {
    console.log(`Project():`,project);
    const {id, name} = project;

  function handleEdit() {
    onEdit(project);
  }
  function handleDelete() {
    onDelete(project.id);
  }

  return (
    <div>
        <div className="grid grid-cols-[var(--project-cols)] gap-4 items-center border-b border-gray-200 py-2">
            <div>{id}</div>
            <div>{name}</div>
            <div>
                <button onClick={handleEdit} className="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">Edit</button>
            </div>
            <div>
                <button onClick={handleDelete} className="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">Delete</button>
            </div>
        </div>
    </div>
  )
}

export default Project
