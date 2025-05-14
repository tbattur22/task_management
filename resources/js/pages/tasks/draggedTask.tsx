import React, {forwardRef} from 'react';
import Task from '@/pages/tasks/task';
import { TaskType } from '@/types';

type DraggedTaskProps = {
  task: TaskType;
}
export const DraggedTask = forwardRef(({task, ...props}: DraggedTaskProps, ref: React.ForwardedRef<HTMLDivElement>) => {
  return (
    <div {...props} ref={ref}>
      <Task
        task={task}
        isOverlay={true}
       />
    </div>
  )
});
