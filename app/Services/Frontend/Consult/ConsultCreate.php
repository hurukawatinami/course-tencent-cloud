<?php

namespace App\Services\Frontend\Consult;

use App\Models\Chapter as ChapterModel;
use App\Models\Consult as ConsultModel;
use App\Models\Course as CourseModel;
use App\Models\User as UserModel;
use App\Services\Frontend\ChapterTrait;
use App\Services\Frontend\CourseTrait;
use App\Services\Frontend\Service as FrontendService;
use App\Validators\Consult as ConsultValidator;
use App\Validators\UserDailyLimit as UserDailyLimitValidator;

class ConsultCreate extends FrontendService
{

    use CourseTrait;
    use ChapterTrait;

    public function handle()
    {
        $post = $this->request->getPost();

        $user = $this->getLoginUser();

        $chapter = $this->checkChapter($post['chapter_id']);

        $course = $this->checkCourse($chapter->course_id);

        $validator = new UserDailyLimitValidator();

        $validator->checkConsultLimit($user);

        $validator = new ConsultValidator();

        $question = $validator->checkQuestion($post['question']);

        $validator->checkIfDuplicated($chapter->id, $user->id, $question);

        $priority = $this->getPriority($course, $user);

        $consult = new ConsultModel();

        $consult->question = $question;
        $consult->priority = $priority;
        $consult->course_id = $course->id;
        $consult->chapter_id = $chapter->id;
        $consult->user_id = $user->id;
        $consult->published = 1;

        $consult->create();

        $this->incrCourseConsultCount($course);
        $this->incrChapterConsultCount($chapter);
        $this->incrUserDailyConsultCount($user);

        return $consult;
    }

    protected function getPriority(CourseModel $course, UserModel $user)
    {
        $charge = $course->market_price > 0;
        $vip = $user->vip == 1;

        if ($vip && $charge) {
            $priority = ConsultModel::PRIORITY_HIGH;
        } elseif ($charge) {
            $priority = ConsultModel::PRIORITY_MIDDLE;
        } else {
            $priority = ConsultModel::PRIORITY_LOW;
        }

        return $priority;
    }

    protected function incrCourseConsultCount(CourseModel $course)
    {
        $this->eventsManager->fire('courseCounter:incrConsultCount', $this, $course);
    }

    protected function incrChapterConsultCount(ChapterModel $chapter)
    {
        $this->eventsManager->fire('chapterCounter:incrConsultCount', $this, $chapter);
    }

    protected function incrUserDailyConsultCount(UserModel $user)
    {
        $this->eventsManager->fire('userDailyCounter:incrConsultCount', $this, $user);
    }

}
