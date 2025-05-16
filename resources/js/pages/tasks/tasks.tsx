import { useState, useEffect, useCallback } from 'react';
import { ToastContainer, toast } from 'react-toastify';
import axios from 'axios';
import Task from '@/pages/tasks/task';
import { type SharedData, ProjectType, TaskType } from '@/types';
import { Head, Link, usePage, router } from '@inertiajs/react';
import {
  DndContext,
  closestCenter,
  DragOverlay,
  KeyboardSensor,
  PointerSensor,
  useSensor,
  useSensors,
  type DragEndEvent,
  DragStartEvent
} from '@dnd-kit/core';
import {
  arrayMove,
  SortableContext,
  sortableKeyboardCoordinates,
  verticalListSortingStrategy,
} from '@dnd-kit/sortable';
import {
  restrictToVerticalAxis,
} from '@dnd-kit/modifiers';
import {DraggedTask} from './draggedTask';

type TasksProps = {
    projects: ProjectType[],
    selectedProject: ProjectType,
    tasks: TaskType[]
}

export default function Tasks({projects, selectedProject, tasks} : TasksProps) {
    const { flash } = usePage<SharedData>().props;
    const [tasksData, setTasksData] = useState<TaskType[]>(tasks);
    const [activeTask, setActiveTask] = useState<TaskType | null>(null);
    const [dragDropped, setDragDropped] = useState(false);

    const setSelectedProject = (projectId:string) => {
        router.post(route('project.select',projectId));
    }
    const handleCreate = () => {
        router.post(route('task.create',selectedProject.id));
    };

    const handleEdit = (taskToEdit: TaskType) => {
        router.get(route('task.edit',taskToEdit.id));
    };

    const handleDelete = (id:number) => {
      const foundTask = tasksData.find(task => task.id === id);
      if (!foundTask) throw new Error(`The task with id ${id} not found!`);

      if (window.confirm(`Are you sure you want to delete the task: ${foundTask.name}?`)) {
          router.delete(route('task.destroy', id));
      }
    };

    const sensors = useSensors(
      useSensor(PointerSensor),
      useSensor(KeyboardSensor, {
        coordinateGetter: sortableKeyboardCoordinates,
      })
    );

    function handleDragStart(event: DragStartEvent) {
      const {active} = event;
      const foundTask = tasksData.find(task => task.id === active.id);
      if (foundTask) {
        setActiveTask(foundTask);
      } else {
        throw new Error("Failed to find task with id " + active.id);
      }
    }
    function handleDragEnd(event:DragEndEvent) {
        const {active, over} = event;

        if (over && active.id !== over.id) {
            setTasksData(items => {
                const activeIndex = items.findIndex((item) => item.id === active.id);
                const overIndex = items.findIndex((item) => item.id === over.id);
                return arrayMove(items, activeIndex, overIndex);
            });
            setDragDropped(true);
        }
    }

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
   }, [dragDropped, tasksData]);

    // if any flash message returned from server display it
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
                <DndContext
                  sensors={sensors}
                  collisionDetection={closestCenter}
                  onDragStart={handleDragStart}
                  onDragEnd={handleDragEnd}
                  modifiers={[restrictToVerticalAxis]}>
                  <SortableContext items={tasksData} strategy={verticalListSortingStrategy}>
                    {tasksData?.length > 0 ? (
                        tasksData?.map((task) => (
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
                  </SortableContext>
                  <DragOverlay>
                    {activeTask ? <DraggedTask task={activeTask} /> : null}
                  </DragOverlay>
                </DndContext>
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
