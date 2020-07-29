<?php

namespace App\Http\Admin\Controllers;

use App\Services\Vod as VodService;
use Phalcon\Mvc\View;

/**
 * @RoutePrefix("/admin/vod")
 */
class VodController extends Controller
{

    /**
     * @Post("/upload/sign", name="admin.vod.upload_sign")
     */
    public function uploadSignAction()
    {
        $vodService = new VodService();

        $sign = $vodService->getUploadSign();

        return $this->jsonSuccess(['sign' => $sign]);
    }

    /**
     * @Get("/player", name="admin.vod.player")
     */
    public function playerAction()
    {
        $chapterId = $this->request->getQuery('chapter_id');
        $playUrl = $this->request->getQuery('play_url');

        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->pick('public/vod_player');
        $this->view->setVar('chapter_id', $chapterId);
        $this->view->setVar('play_url', urldecode($playUrl));
    }

}
