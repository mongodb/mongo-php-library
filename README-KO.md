# MongoDB PHP 라이브러리

![Tests](https://github.com/mongodb/mongo-php-library/workflows/Tests/badge.svg)
![Coding Standards](https://github.com/mongodb/mongo-php-library/workflows/Coding%20Standards/badge.svg)

이 라이브러리는 하위 레벨 [PHP 드라이버](https://github.com/mongodb/mongo-php-driver)(`mongodb 확장`)에 대한 높은 수준의 추상화를 제공합니다.
이 확장은 명령, 쿼리, 쓰기 작업을 실행할 수 있는 제한된 API를 제공하지만 이 라이브러리는 다른 MongoDB 드라이버와 유사한 완전한 기능을 갖춘 API를 구현한다.
클라이언트, 데이터베이스 및 수집 개체에 대한 추상화가 포함되어 있으며 CRUD 작업 및 공통 명령(예시: 인덱스 및 수집 관리)을 위한 메소드를 제공합니다.
MongoDB로 애플리케이션을 개발할 경우 확장 기능만 사용하는 것이 아니라 이 라이브러리 또는 다른 고급 추상화를 사용하는 것을 고려해야 합니다.
이 [라이브러리의 아키텍처](https://php.net/manual/en/mongodb.overview.php)와 MongoDB 확장에 대한 추가 정보는 다음에서 확인할 수 있습니다.

## 문서화

 - https://docs.mongodb.com/php-library/
 - https://docs.mongodb.com/ecosystem/drivers/php/

## 설치

이 라이브러리를 설치할 때 선호되는 방법은 프로젝트 루트에서 다음을 실행하여 [Composer](https://getcomposer.org/)를 사용하는 것입니다.


    $ composer require mongodb/mongodb


추가 설치 지침은 [라이브러리 설명서](https://docs.mongodb.com/php-library/current/tutorial/install-php-library/)에서 확인할 수 있습니다.

이 라이브러리는 드라이버를 위한 고급 추상화이므로 `mongodb` 확장도 설치해야 합니다.

    $ pecl install mongodb
    $ echo "extension=mongodb.so" >> `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`

확장을 위한 추가 설치 지침은 다음에서 확인할 수 있습니다
[PHP.net 문서](https://www.php.net/manual/en/mongodb.installation.php).

## 문제 보고

라이브러리에 대한 문제는 몽고DB의 JIRA의 [PHPLIB](https://jira.mongodb.org/secure/CreateIssue!default.jspa?project-field=PHPLIB) 프로젝트에서 보고되어야 합니다. 
연장 관련 문제는 [PHPC](https://jira.mongodb.org/secure/CreateIssue!default.jspa?project-field=PHPC) 프로젝트에 보고해야 합니다.


일반적인 질문 및 지원 요청은 MongoDB의 [기술 지원](https://docs.mongodb.com/manual/support/) 채널 중 하나를 사용하십시오.


### 보안 취약성

드라이버나 다른 MongoDB 프로젝트에서 보안 취약점을 확인했으면 [취약성 보고서 만들기](https://docs.mongodb.org/manual/tutorial/create-a-vulnerability-report)의 지침에 따라 보고하십시오.

## 개발

개발은 MongoDB의 프로젝트 JIRA의 [PHPLIB](https://jira.mongodb.org/projects/PHPLIB/summary) 에서 추적됩니다.
이 프로젝트에 기여하기 위한 문서는 [CONTRIBUTING.md](CONTRIBUTING.md) 에서 찾을 수 있습니다.


