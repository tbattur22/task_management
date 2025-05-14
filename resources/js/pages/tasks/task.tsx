import {ItemTypes} from '../../lib/DnDItemTypes'
import {TaskType} from '@/types'
import {useSortable} from '@dnd-kit/sortable';
import {CSS} from '@dnd-kit/utilities';


type TaskProps = {
  task: TaskType;
  onEdit?: (task:TaskType)=>void;
  onDelete?:(id:number)=>void;
  isOverlay?: boolean;
};

const Task = ({task, onEdit, onDelete, isOverlay}: TaskProps)=> {
    const { id, name, priority, project_id, created_at, updated_at } = task;
    const {
        attributes,
        listeners,
        setNodeRef,
        setActivatorNodeRef,
        transform,
        transition,
    } = useSortable({id});

    const style = {
        transform: CSS.Transform.toString(transform),
        transition,
        opacity: isOverlay ? 0.8 : 1
    };

  return (
    <div ref={setNodeRef} style={style} className="flex justify-between task bg-white shadow-md rounded-lg p-4 border border-gray-200 hover:shadow-lg transition duration-300">
      <div>
        <div className="flex justify-between items-start">
          <div>
            <h3 className="text-lg font-semibold text-gray-800">{name}</h3>
            <p className="text-sm text-gray-500">Id: {id}</p>
            <p className="text-sm text-gray-500">Project Id: {project_id}</p>
            <p className="text-sm text-gray-500">Priority: {priority}</p>
          </div>
        </div>
        <div className="text-sm text-gray-600 mt-2">
          <p>Created: {new Date(created_at).toLocaleString()}</p>
          <p>Updated: {new Date(updated_at).toLocaleString()}</p>
        </div>
        <div className="flex gap-2 mt-4">
          <button
            onClick={() => onEdit ? onEdit(task) : null}
            className="bg-blue-500 text-white px-3 py-1 text-sm rounded hover:bg-blue-600"
          >
            Edit
          </button>
          <button
            onClick={() => onDelete ? onDelete(task.id) : null}
            className="bg-red-500 text-white px-3 py-1 text-sm rounded hover:bg-red-600"
          >
            Delete
          </button>
        </div>
      </div>
      <div >
        <button ref={setActivatorNodeRef} {...attributes} {...listeners} className='cursor-move'>
          <svg  xmlns="http://www.w3.org/2000/svg"  width={24}  height={24}  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  strokeWidth={2}  strokeLinecap="round"  strokeLinejoin="round"  className="icon icon-tabler icons-tabler-outline icon-tabler-drag-drop"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19 11v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2" /><path d="M13 13l9 3l-4 2l-2 4l-3 -9" /><path d="M3 3l0 .01" /><path d="M7 3l0 .01" /><path d="M11 3l0 .01" /><path d="M15 3l0 .01" /><path d="M3 7l0 .01" /><path d="M3 11l0 .01" /><path d="M3 15l0 .01" /></svg>
        </button>
      </div>
    </div>
  )
}

export default Task
