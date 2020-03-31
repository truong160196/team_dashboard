<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Model\History;
use App\Model\Server;
use App\Model\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AjaxController extends Controller
{
  //
    public function getList(Request $request)
    {
        try {
            $access_token = DB::table('access_tokens')
                ->where('key', '=' , $request->accessToken)
                ->first();

            if (!$access_token) {
                $res = [];
                return response()->json($res, 200);
            }

            $projectDb = DB::table('workspaces')
                ->select(
                    'histories.project_id',
                    'workspaces.name',
                    'workspaces.key',
                    DB::raw('count(histories.project_id) as history')
                )
                ->leftJoin('histories', 'histories.project_id', 'workspaces.id')
                ->where('workspaces.isDelete', '=' , 0)
                ->groupBy(
                    'histories.project_id',
                    'workspaces.name',
                    'workspaces.key'
                )
                ->get();

            return response()->json($projectDb, 200);

        } catch (\Exception $e) {
            $res = [
                'message' => $e->getMessage()
            ];

            return response()->json($res, 500);
        }
    }

    public function getListHistory(Request $request)
    {
        try {
            $access_token = DB::table('access_tokens')
                ->where('key', '=' , $request->accessToken)
                ->first();

            if (!$access_token) {
                $res = [];
                return response()->json($res, 200);
            }

            $historiesDb = DB::table('histories')
                ->join("workspaces", "workspaces.id", "=", "histories.project_id")
                ->orderBy('histories.id', 'desc')
                ->get();

            return response()->json($historiesDb, 200);

        } catch (\Exception $e) {
            $res = [
                'message' => $e->getMessage()
            ];

            return response()->json($res, 500);
        }
    }

    public function getDetail($key, Request $request)
    {
        try {
            $access_token = DB::table('access_tokens')
                ->where('key', '=' , $request->accessToken)
                ->first();

            if (!$access_token) {
                $res = [
                    'message' => 'Access token not exist'
                ];
                return response()->json($res, 503);
            }

            $projectDb = DB::table('workspaces')
                ->where('key', '=' , $key)
                ->where('workspaces.isDelete', '=' , 0)
                ->first();

            if ($projectDb->path) {
                $contents = Storage::get($projectDb->path);


                $historyCount = DB::table('histories')
                    ->where('project_id', '=' , $projectDb->id)
                    ->count();

                $res = [
                    'content' => $contents,
                    'countHistory' => $historyCount,
                ];
                return response()->json($res);
            }

            $res = [
                'message' => 'can not found file yml'
            ];

            return response()->json($res, 404);

        } catch (\Exception $e) {
            $res = [
                'message' => $e->getMessage()
            ];

            return response()->json($res, 500);
        }
    }

    public function pushFileProjectToServer(Request $request)
    {
        try {
            DB::beginTransaction();

            $access_token = DB::table('access_tokens')
                ->where('key', '=' , $request->accessToken)
                ->first();

            if (!$access_token) {
                $res = [
                    'message' => 'Access token not exist'
                ];
                return response()->json($res, 503);
            }


            // check project been deleted
            $projectDelete = DB::table('workspaces')
                ->where('key', '=' , $request->key)
                ->where('isDelete', '=' , 1)
                ->first();

            if ($projectDelete && $projectDelete->id) {
                DB::rollback();

                $res = [
                    'message' => 'Project been deleted!'
                ];
                return response()->json($res, 503);
            }

            $projectDb = DB::table('workspaces')
                ->where('key', '=' , $request->key)
                ->first();

            if ($projectDb && $projectDb->id) {
                $historyCount = DB::table('histories')
                    ->where('project_id', '=', $projectDb->id)
                    ->count();

                if ($historyCount > $request->countHistory) {
                    DB::rollback();

                    $res = [
                        'message' => 'Please pull file from server before push new content'
                    ];
                    return response()->json($res, 503);
                }

            }

                $name = now()->timestamp;

            if ($request->key) {
                $name = $request->key;
            }

            $destinationPath = 'workspace/' . $name .'.yml';

            Storage::disk('local')->put($destinationPath, $request->contents);

            if ($projectDb && $projectDb->id) {
                $dataCommit = [];

                foreach ($request->lstCommit as $commit){
                    $commits = [
                        'user' => $commit["user"],
                        'comment' => $commit["comment"],
                        'date' => $commit["date"],
                        'project_id' => $projectDb->id
                    ];

                    array_push($dataCommit, $commits);
                }

                $idHistoryUpdate = History::insert($dataCommit);

                if (!$idHistoryUpdate) {
                    DB::rollback();

                    $res = [
                        'message' => 'Can not save history'
                    ];

                    return response()->json($res, 503);
                }

                $res = [
                    'message' => 'Update file success'
                ];


                DB::commit();

                return response()->json($res, 200);
            }

            $dataCreate = [
                'name' => $request->name,
                'key' => $request->key,
                'path' => $destinationPath,
                'access_id' => $access_token->id,
            ];

            $projectCreated = Workspace::create($dataCreate);

            if (!$projectCreated) {
                DB::rollback();
                $res = [
                    'message' => 'Can not create file'
                ];
                return response()->json($res, 503);
            }

            $dataCommit = [];

            foreach ($request->lstCommit as $commit){
                $commits = [
                    'user' => $commit["user"],
                    'comment' => $commit["comment"],
                    'date' => $commit["date"],
                    'project_id' => $projectCreated->id
                ];

                array_push($dataCommit, $commits);
            }

            $idHistory = History::insert($dataCommit);

            if (!$idHistory) {
                DB::rollback();

                $res = [
                    'message' => 'Can not save history'
                ];

                return response()->json($res, 503);
            }

            DB::commit();

            $res = [
                'message' => 'Create file success'
            ];

            return response()->json($res);
        } catch (\Exception $e) {
            $res = [
                'message' => $e->getMessage()
            ];

            return response()->json($res, 500);
        }

    }

    public function deleteProject(Request $request)
    {
        try {
            DB::beginTransaction();

            $access_token = DB::table('access_tokens')
                ->where('key', '=' , $request->accessToken)
                ->Where('name', '=' , 'admin')
                ->first();

            if (!$access_token) {
                $res = [
                    'message' => 'Access token not exist or not permission!'
                ];
                return response()->json($res, 503);
            }

            $projectDb = DB::table('workspaces')
                ->where('key', '=' , $request->key)
                ->first();

            if (!$projectDb->id) {
                $res = [
                    'message' => 'Record not found!'
                ];
                return response()->json($res, 404);
            }

            $projectUpdate = DB::table('workspaces')
                ->where('key', '=' , $request->key)
                ->update(['isDelete' => 1]);

            if (!$projectUpdate) {
                DB::rollback();

                $res = [
                    'message' => 'Can not delete project'
                ];

                return response()->json($res, 503);
            }

            $dataDeleteHistory = [
                'user' => $request->user,
                'comment' => $request->comment,
                'date' => $request->date,
                'project_id' => $projectDb->id
            ];

            $idHistory = History::create($dataDeleteHistory);

            if (!$idHistory) {
                DB::rollback();

                $res = [
                    'message' => 'Can not save history'
                ];

                return response()->json($res, 503);
            }

            DB::commit();

            $res = [
                'message' => 'Delete project successfully!'
            ];
            return response()->json($res, 200);

        } catch (\Exception $e) {
            $res = [
                'message' => $e->getMessage()
            ];

            return response()->json($res, 500);
        }
    }

    function get_headers($url,$format=0)
    {
        $url=parse_url($url);
        $end = "\r\n\r\n";
        $fp = fsockopen($url['host'], (empty($url['port'])?80:$url['port']), $errno, $errstr, 30);
        if ($fp)
        {
            $out  = "GET / HTTP/1.1\r\n";
            $out .= "Host: ".$url['host']."\r\n";
            $out .= "Connection: Close\r\n\r\n";
            $var  = '';
            fwrite($fp, $out);
            while (!feof($fp))
            {
                $var.=fgets($fp, 1280);
                if(strpos($var,$end))
                    break;
            }
            fclose($fp);

            $var=preg_replace("/\r\n\r\n.*\$/",'',$var);
            $var=explode("\r\n",$var);
            if($format)
            {
                foreach($var as $i)
                {
                    if(preg_match('/^([a-zA-Z -]+): +(.*)$/',$i,$parts))
                        $v[$parts[1]]=$parts[2];
                }
                return $v;
            }
            else
                return $var;
        }
    }

    public function connectServer(Request $request)
    {
        try {
//            $commands = array(
//                'cd /var/www/html_swagger',
//                'echo "Some line" > file1.txt',
//            );
//
//            \SSH::into('production')->run($commands, function($line)
//            {
//                echo $line.PHP_EOL;
//            });

            $url = $request->host;

            $httpCode = get_headers($url, 1);

            $res = array(
                'status' => 0,
                'message' => json_encode($httpCode),
            );

            if (array_search("HTTP/1.1 200 OK", $httpCode) || array_search("HTTP/1.0 200 OK", $httpCode)) {
                $res["status"] = 200;
            }

            return response()->json($res, 200);
        } catch (\Exception $e) {
            $res = [
                'status' => 0,
                'message' => $e->getMessage()
            ];
            return response()->json($res, 500);
        }
    }

    public function command(Request $request)
    {
        try {
            $commands = array(
                $request->command,
            );

            $this->content = "";

            \SSH::into('production')->run($commands, function($line)
            {
                $this->content .= $line.PHP_EOL;
            });

            $res = array(
                'status' => 0,
                'message' =>  $this->content,
            );


            return response()->json($res, 200);
        } catch (\Exception $e) {
            $res = [
                'status' => 0,
                'message' => $e->getMessage()
            ];
            return response()->json($res, 500);
        }
    }



    public function getLogsFile(Request $request)
    {
        try {
            $commands = array(
                "cat " . $request->pathLog,
            );

            $this->content = "";

            \SSH::into('production')->run($commands, function($line)
            {
                $this->content .= $line;
            });

            $res = [
                'message' => $this->content,
            ];

            return response()->json($res, 200);
        } catch (\Exception $e) {
            $res = [
                'message' => $e->getMessage()
            ];
            return response()->json($res, 500);
        }
    }


    public function getListServer(Request $request)
    {
        try {
            $lstServer = DB::table('servers')
                ->get();

            return response()->json($lstServer, 200);

        } catch (\Exception $e) {
            $res = [
                'message' => $e->getMessage()
            ];

            return response()->json($res, 500);
        }
    }

    public function addNewServer(Request $request)
    {
        try {
            DB::beginTransaction();

            $dataServer = [
                'nameServer' => $request->nameServer,
                'urlServer' => $request->urlServer,
                'pathSource' => $request->pathSource,
                'pathLog' => $request->pathLogFile,
                'scriptStart' => $request->scriptStart,
                'scriptStop' => $request->scriptStop,
                'scriptTask' => $request->scriptTask,
            ];

            $idServer = Server::create($dataServer);

            if (!$idServer) {
                DB::rollback();

                $res = [
                    'message' => 'Can not add new server'
                ];
                return response()->json($res, 503);
            }

            DB::commit();

            $res = array(
                'message' => 'Add server successfully!',
            );

            return response()->json($res, 200);
        } catch (\Exception $e) {
            $res = [
                'status' => 0,
                'message' => $e->getMessage()
            ];
            return response()->json($res, 500);
        }
    }
}
