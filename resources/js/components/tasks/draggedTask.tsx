import React, {forwardRef} from 'react';
import Task from '@/components/tasks/task';

export const DraggedTask = forwardRef(({task, ...props}, ref) => {
  return (
    <div {...props} ref={ref}>
      <Task
        task={task}
        isOverlay={true}
       />
    </div>
  )
});
