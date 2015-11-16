<?php

namespace Jaccob\MediaBundle\Toolkit;

use Symfony\Component\Process\ProcessBuilder;

class ExternalFFMpegVideoToolkit implements VideoToolkitInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDimensions($inFile)
    {
        throw new \Exception("Not implemented yet");
    }

    /**
     * {@inheritdoc}
     */
    public function generateThumbnail($inFile, $outFile)
    {
        if (file_exists($outFile)) {
            unlink($outFile);
        }

        (new ProcessBuilder())
            ->setPrefix("ffmpeg")
            ->setArguments([
                "-i", "$inFile",
                "-ss", "00:00:01.000",
                "-vframes", "1",
                $outFile,
            ])
            ->getProcess()
            ->mustRun()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findMetaData($inFile)
    {
        /*
         * Sample output
         *
        ffprobe \
            -v error \
            -show_format \
            -show_streams \
            /var/www/jaccob/web/media/th/full/fZZH7DFHCfxzI4nyU5mbud5pTohqxtsKGmNQ6ZGi7PmfzAHd6pcxyCVIoCW/OaqrUpgL56qOfkgTF7kK3a8Mbw.MOV
        [STREAM]
        index=0
        codec_name=h264
        codec_long_name=H.264 / AVC / MPEG-4 AVC / MPEG-4 part 10
        profile=Constrained Baseline
        codec_type=video
        codec_time_base=1/48000
        codec_tag_string=avc1
        codec_tag=0x31637661
        width=1920
        height=1080
        coded_width=1920
        coded_height=1088
        has_b_frames=0
        sample_aspect_ratio=0:1
        display_aspect_ratio=0:1
        pix_fmt=yuvj420p
        level=50
        color_range=pc
        color_space=bt709
        color_transfer=bt709
        color_primaries=bt709
        chroma_location=left
        timecode=N/A
        refs=1
        is_avc=1
        nal_length_size=4
        id=N/A
        r_frame_rate=24000/1001
        avg_frame_rate=24000/1001
        time_base=1/24000
        start_pts=0
        start_time=0.000000
        duration_ts=593593
        duration=24.733042
        bit_rate=32662202
        max_bit_rate=N/A
        bits_per_raw_sample=8
        nb_frames=593
        nb_read_frames=N/A
        nb_read_packets=N/A
        DISPOSITION:default=1
        DISPOSITION:dub=0
        DISPOSITION:original=0
        DISPOSITION:comment=0
        DISPOSITION:lyrics=0
        DISPOSITION:karaoke=0
        DISPOSITION:forced=0
        DISPOSITION:hearing_impaired=0
        DISPOSITION:visual_impaired=0
        DISPOSITION:clean_effects=0
        DISPOSITION:attached_pic=0
        TAG:creation_time=2014-01-19 00:22:50
        TAG:language=eng
        [/STREAM]
        [STREAM]
        index=1
        codec_name=pcm_s16le
        codec_long_name=PCM signed 16-bit little-endian
        profile=unknown
        codec_type=audio
        codec_time_base=1/48000
        codec_tag_string=sowt
        codec_tag=0x74776f73
        sample_fmt=s16
        sample_rate=48000
        channels=2
        channel_layout=stereo
        bits_per_sample=16
        id=N/A
        r_frame_rate=0/0
        avg_frame_rate=0/0
        time_base=1/48000
        start_pts=0
        start_time=0.000000
        duration_ts=1187186
        duration=24.733042
        bit_rate=1536000
        max_bit_rate=N/A
        bits_per_raw_sample=N/A
        nb_frames=1187186
        nb_read_frames=N/A
        nb_read_packets=N/A
        DISPOSITION:default=1
        DISPOSITION:dub=0
        DISPOSITION:original=0
        DISPOSITION:comment=0
        DISPOSITION:lyrics=0
        DISPOSITION:karaoke=0
        DISPOSITION:forced=0
        DISPOSITION:hearing_impaired=0
        DISPOSITION:visual_impaired=0
        DISPOSITION:clean_effects=0
        DISPOSITION:attached_pic=0
        TAG:creation_time=2014-01-19 00:22:50
        TAG:language=eng
        [/STREAM]
        [FORMAT]
        filename=/var/www/jaccob/web/media/th/full/fZZH7DFHCfxzI4nyU5mbud5pTohqxtsKGmNQ6ZGi7PmfzAHd6pcxyCVIoCW/OaqrUpgL56qOfkgTF7kK3a8Mbw.MOV
        nb_streams=2
        nb_programs=0
        format_name=mov,mp4,m4a,3gp,3g2,mj2
        format_long_name=QuickTime / MOV
        start_time=0.000000
        duration=24.733042
        size=105826508
        bit_rate=34230001
        probe_score=100
        TAG:major_brand=qt  
        TAG:minor_version=537331968
        TAG:compatible_brands=qt  CAEP
        TAG:creation_time=2014-01-19 00:22:50
        [/FORMAT]
         */

      $output = (new ProcessBuilder())
          ->setPrefix("ffprobe")
          ->setArguments([
              "-v", "error",
              "-show_format",
              "-show_streams",
              $inFile,
          ])
          ->getProcess()
          ->mustRun()
          ->getOutput()
      ;

      $data = [];
      $section = 'unclassified';
      $sections = [];

      // Not proud of this one (tm)
      foreach (explode("\n", $output) as $line) {
          if (strpos($line, '=')) {

              list ($key, $value) = explode('=', $line);

              if ('stream' === $section) {
                  $data[$section][$sections[$section]][$key] = $value;
              } else {
                  $data[$section][$key] = $value;
              }
  
          } else {
              // Start or end a section, drops [/SECTION]
              $matches = [];
              if (preg_match('@^\[(.*)\]$@', $line, $matches)) {
                  $section = strtolower($matches[1]);
                  if (isset($sections[$section])) {
                      $sections[$section]++;
                  } else {
                      $sections[$section] = 0;
                  }
              }
          }
      }

      return $data;
    }

    /**
     * {inheritdoc}
     */
    public function transcode($inFile, $outFile, $video, $format, $audio = null, $options = [])
    {
        $arguments = [
          '-i',     $inFile,
          '-c:v',   $video,
          '-f',     $format,
        ];
        if ($audio) {
            $arguments[] = '-c:a';
            $arguments[] = $audio;
        }

        // Strip some options that should normally come from the defined formats
        // in the parameters.yml file, see the documentation in there
        unset(
            $options['video'],
            $options['format'],
            $options['audio']
        );
        foreach ($options as $key => $value) {
            // Just in case, you never now
            if (null === $value || '' === $value) {
                $arguments[] = '-' . $key;
            } else {
                $arguments[] = '-' . $key;
                $arguments[] = $value;
            }
        }

        // Some codecs are flaged as experimental, do not let ffmpeg fail
        if (!isset($options['strict'])) {
            $arguments[] = '-strict';
            $arguments[] = '-2';
        }
        // All processors today are smp enought to enable this per default
        if (!isset($options['threads'])) {
            $arguments[] = '-threads';
            $arguments[] = 2;
        }
        // Always force ffpmeg to replace existing files
        $arguments[] = '-y';

        // Most important argument of all
        $arguments[] = $outFile;

        // @todo Output should go somewhere
        $output = (new ProcessBuilder())
            ->setTimeout(null)
            ->setPrefix("ffmpeg")
            ->setArguments($arguments)
            ->getProcess()
            ->mustRun()
            ->getOutput()
        ;
    }
}
