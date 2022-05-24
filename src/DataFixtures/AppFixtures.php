<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use App\Entity\Tag;
use App\Entity\Team;
use App\Entity\Test;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Result;
use App\Entity\TagTest;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {   
        $tagList = [];
        $Tags = ['Vitesse','Force','Endurance','Technique','Physique','Dribbles','Passes','Jongles'];
        foreach($Tags as $value){
            $tag = new Tag();
            $tag->setName($value);
            $tagList [] = $tag;
            $manager->persist($tag);

        }
        $listTeam = [];
        $Teams = ['Manchester.Utd','Liverpool','Arsenal','OL','Manchester City','Real Madrid','F.C Barcelone'];
        foreach($Teams as $value){
            $team = new Team();
            $team->setName($value);
            $team->setAgeCategory('U'.random_int(5,20));
            $team->setStatus(1);
            $listTeam [] = $team;
            $manager->persist($team);
        }
        $listTest = [];
        for($x = 1; $x <20; $x++){
            $test = new Test();
            $tagTest = new TagTest();
            $tagTest->setTag($tagList[random_int(3,4)]);
            $tagTest->setIsPrimary(1);
            $tagTest->setTest($test);
            $manager->persist($tagTest);
            $tagTest2 = new TagTest();
            $tagTest2->setTag($tagList[random_int(0,7)]);
            $tagTest2->setIsPrimary(1);
            $tagTest2->setTest($test);
            $manager->persist($tagTest2);
            $test->setName('Test'.$x);
            $test->setDescription('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.');
            $test->setUnit('mètres');
            $test->setSlug($x);
            $listTest [] = $test;
            $manager->persist($test);
        }
        $roles = ['joueur','coach','admin'];
        $listUsersObject = [];
        for($x = 1; $x <=50; $x++){
            $user = new User();
            $user->setFirstname('user'.$x);
            $user->setLastname('lastname');
            $user->setEmail($roles[random_int(0,1)]."@".$user->getFirstname().".com");
            $user->setBirthdate(new DateTime('now'));
            $user->setStatus(1);
            if(str_contains($user->getEmail(),'coach')){
                $activity = new Activity();
                $activity->setUser($user);
                $activity->setTeam($listTeam[random_int(0,6)]);
                $activity->setRole(random_int(0,1));
                $manager->persist($activity);
                $user->addActivity($activity);
            }
            $activity2 = new Activity();
            $activity2->setUser($user);
            $activity2->setTeam($listTeam[random_int(0,6)]);
            if(str_contains($user->getEmail(),'coach')){
                $activity2->setRole(1);
            }else{
                $activity2->setRole(0);
            }
            $manager->persist($activity2);
            $user->addActivity($activity2);
            $user->setPassword(password_hash($user->getFirstname(), PASSWORD_DEFAULT));
            $explode = explode('@',$user->getEmail());
            $user->setRoles(['ROLE_'.strtoupper($explode[0])]);
            $user->setSlug($user->getFirstname());
            $listUsersObject [] = $user;
            $manager->persist($user);
        }
        
        

        
        for($y = 1; $y<150;$y++){
            $result = new Result();
            $result->setResult(random_int(1,124));
            $result->setUser($listUsersObject[random_int(0,49)]);
            $result->setStatus(random_int(0,1));
            $result->setDoneAt(new DateTime('now'));
            $result->setTest($listTest[random_int(0,18)]);
            $manager->persist($result);
        }


        $manager->flush();
    }
}
