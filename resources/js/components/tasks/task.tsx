import { useDrag, useDrop } from 'react-dnd'
import {ItemTypes} from '../../lib/DnDItemTypes'
import {TaskType} from '@/types'

type TaskProps = {
  index: number,
  task: TaskType,
  onEdit: (task:TaskType)=>void,
  onDelete:(id:number)=>void,
  moveTask:(from:number, to:number)=>void
};

const Task = ({index, task, onEdit, onDelete, moveTask}: TaskProps)=> {
    const { id, name, priority, project_id, created_at, updated_at } = task;

    const [{ isDragging }, drag] = useDrag(() => ({
      type: ItemTypes.TASK,
      item: { id, name, priority, index },
      end: (item, monitor) => {
        const dropResult = monitor.getDropResult<any>()
        if (item && dropResult) {
          // alert(`You dropped ${item.name} from index ${dropResult.fromIndex} to ${dropResult.toIndex}!`)
          moveTask(dropResult.fromIndex, dropResult.toIndex);
        }
      },
      collect: (monitor) => ({
        isDragging: monitor.isDragging(),
        handlerId: monitor.getHandlerId(),
      }),
    }));

    const [{canDrop, isOver}, drop] = useDrop({
      accept: ItemTypes.TASK,
      canDrop: (item, monitor) => {
        // console.log(`move canDrop(): item:`,item);
        return item?.priority && item.priority !== priority;
      },
      drop: (item, monitor) => {
        console.log(`move drop(): src item:target item`,item, task);
        // moveTask(item.index, index);
        return {fromIndex: item.index, toIndex: index};
      },
      collect: (monitor) => ({
        isOver: monitor.isOver(),
        canDrop: monitor.canDrop()
      })
    });

  return (
    <div ref={node => drag(drop(node))} className="task bg-white shadow-md rounded-lg p-4 border border-gray-200 hover:shadow-lg transition duration-300">
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
          onClick={() => onEdit(task)}
          className="bg-blue-500 text-white px-3 py-1 text-sm rounded hover:bg-blue-600"
        >
          Edit
        </button>
        <button
          onClick={() => onDelete(task.id)}
          className="bg-red-500 text-white px-3 py-1 text-sm rounded hover:bg-red-600"
        >
          Delete
        </button>
      </div>
    </div>
  )
}

export default Task
