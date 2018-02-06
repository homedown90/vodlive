#! /bin/bash
ffmpeg_path=/root/bin
video_path=$1
temp_m3u8_name=$2
log_path=$3
m3u8_name=${temp_m3u8_name##*/}
m3u8_path=${temp_m3u8_name%/*}

file_name=null
last_name=null

video_m3u8_log=${log_path}

# init input_file and out_putfile
prepare(){
input_file=${video_path}
output_file=${m3u8_path}/${m3u8_name}.m3u8
}

# check path exist or not ,if not exist,mkdir 
checkfile_exist(){
var_path=$1
if [[ ! -e ${var_path}  ]]
  then
  echo "mkdir -p ${var_path}"
  mkdir -p  ${var_path}
  fi
}
video_to_m3u8(){
    ${ffmpeg_path}/ffmpeg -y -i ${video_path} -c copy -f hls -hls_list_size 0 -hls_time 10 ${output_file} &>> ${video_m3u8_log}
    if [ $? -eq 0  ]
    then
        echo   -e "-------------------------------------------------------------------------------------\n\n\n\n" >> ${video_m3u8_log}
        echo   -e "-------------------------------------------------------------------------------------" >> ${video_m3u8_log}
        echo   -e " success ${output_file} to ${m3u8_file} "
    else
        echo   -e "failed ${output_file} to ${m3u8_file}"
        exit 1
    fi
}

if [[ -f ${video_path} && ! -d ${video_path} && -n ${ts_name}  ]]
then
  prepare
  checkfile_exist ${output_file%/*}
  checkfile_exist ${video_m3u8_log%/*}
  echo   -e "------------------------------start:-------------------------------------------------------\n\n\n\n" >> ${video_m3u8_log}
  video_to_m3u8
  echo -e "ok,now you can watch the video in http://www.ctlvl.com/${output_file}"
  echo   -e "------------------------------end:-------------------------------------------------------\n\n\n\n" >> ${video_m3u8_log}
else
  echo "require file path and ts name or file not exits"
  exit 1
fi
