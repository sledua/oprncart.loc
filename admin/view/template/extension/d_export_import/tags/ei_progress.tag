<ei_progress>
<div class="progress">
    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width:{getState().progress?getState().progress:100}%">
        <span class="sr-only"></span>
    </div>
</div>
<div class="progress-info text-center">
    <div if={getState().memory_usaged}>{getState().translate.text_memory_usage} {getState().memory_usaged}</div>
    <div if={getState().progress}>{getState().translate.text_progress} {getState().progress} - 100%</div>
    <div if={getState().current}>{getState().translate.text_current_item} {getState().current}</div>
    <div if={getState().current_sheet}>{getState().translate.text_progress_sheets} {getState().current_sheet}/ {getState().count_sheets}</div>
    <div if={getState().current_file}>{getState().translate.text_progress_files} {getState().current_file}/ {getState().count_files}</div>
    <div if={getState().time_left}>{getState().translate.text_left_time} {getState().time_left}</div>
</div>
</ei_progress>