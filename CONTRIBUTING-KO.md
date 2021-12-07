# MongoDB용 PHP 라이브러리에 기여

## 리포지토리 초기화
라이브러리에 기여하려는 개발자는 해당 라이브러리를 복제하고 [Composer](https://getcomposer.org/)를 사용하여 프로젝트 종속성을 초기화해야 합니다.

```
$ git clone https://github.com/mongodb/mongo-php-library.git
$ cd mongo-php-library
$ composer update
```

Composer는 프로젝트 종속성을 설치하는 것 외에도 다음을 확인합니다.
필요한 확장 버전이 설치되었습니다. 확장 설치 방법은 [여기서](http://php.net/manual/en/mongodb.installation.php) 찾을 수 있다

Composer 설치 지침은 [시작하기](https://getcomposer.org/doc/00-intro.md) 에서 확인할 수 있습니다.

## 테스팅

라이브러리의 테스트 제품군은 Composer별 [PHPUnit Bridge](https://symfony.com/phpunit-bridge) 종속성을 통해 설치되는 [PHPUnit](https://phpunit.de/)을 사용합니다.

테스트 제품군은 다음과 같이 실행할 수 있습니다:

```
$ vendor/bin/simple-phpunit
```

`phpunit.xml.dist` 파일은  테스트 세트의 기본 구성 파일로 사용됩니다. 다양한 PHPUnit 옵션 외에도
`MONGODB_URI` 와 `MONGODB_DATABASE` 환경 변수를 정의합니다. 이 구성을 사용자 정의할 수 있는 방법은 다음을 기반으로 자신만의 `phpunit.xml` 파일을 만드는 것입니다.
 테스트를 서버리스 모드로 실행하려면, set the `MONGODB_IS_SERVERLESS` 환경 변수를 `on`으로 설정합니다.


인증이 필요한 클러스터에 대해 테스트를 실행하려면 `MONGODB_URI` 환경 변수에 지정된 연결 문자열에 자격 증명을 포함하거나 
`MONGODB_USERNAME` 와 `MONGODB_PASSWORD` 환경 변수를 설정하십시오. 환경을 통해 정의된 값은 URI에 있는 자격 증명을 재정의합니다.

기본적으로  `simple-phpunit` 바이너리는 실행 중인 PHP 버전에 대해 올바른 PHPUnit 버전을 선택합니다.
특정 PHPUnit 버전에 대해 테스트를 실행하려면 `SYMFONY_PHPUNIT_VERSION` 환경 변수를 사용합니다.

```
$ SYMFONY_PHPUNIT_VERSION=7.5 vendor/bin/simple-phpunit
```

## 코딩 표준 확인

라이브러리의 코드는 [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer)를 사용하여 확인합니다.
이는 Composer에 의해 개발 종속성으로 설치됩니다. 코드에 스타일 오류가 있는지 확인하려면 'phpcs' 바이너리를 실행하십시오.


```
$ vendor/bin/phpcs
```

수정 가능한 모든 오류를 자동으로 수정하려면 'phpcbf' 바이너리를 사용합니다

```
$ vendor/bin/phpcbf
```

## 문서화

라이브러리에 대한 설명서는 'docs/' 디렉토리에 저장되며 다음과 같이 작성됩니다.
관련 도구 [mongodb/docs-php-library](https://github.com/mongodb/docs-php-library)
저장소의 툴 저장소는 이미 소스를 참조하도록 구성되어 있습니다.

즉, 문서에 대한 변경사항은 적용하기 전에 현지에서 테스트해야 합니다. 다음 단계에 따라 도구 저장소에서 로컬로 문서를 작성하십시오:

 * [mongodb/docs-php-library](https://github.com/mongodb/docs-php-library) 툴 레파지토리를 복제하라.

 * 도구 README에 명시된 대로 [giza](https://pypi.python.org/pypi/giza/)를 설치합니다.
 * 문서의 작업 복사본을 `source/` 디렉터리와 동기화하십시오.
   `rsync -a --delete /path/to/delete-path-to-filename/source/`.
 * `giza make publish`로 문서를 작성하세요. `--level warning` 옵션으로 정보 로그 메시지를 억제할 수 있습니다.
 * 생성된 설명서는 `build/master/html` 디렉터리에서 찾을 수 있습니다.

## 유지 관리 분기 생성 및 마스터 분기 별칭 업데이트

새로운 주 버전 또는 부 버전(예: 1.9.0)을 릴리스한 후에는 유지관리 분기(예: v1.9)를 생성해야 합니다. 
패치 릴리스에 대한 모든 개발(예:1.9.1)은 해당 지점 내에서 수행되며, 다음 주요 또는 부 릴리스에 대한 모든 개발은 마스터에서 계속 진행될 수 있습니다.

유지 관리 분기를 생성한 후 `extra.branch-alias.dev-master` 필드가 표시됩니다.
마스터 분기의 `composer.json` 파일을 업데이트해야 합니다. 예를 들어,
v1.9를 분기한 후에도 마스터 분기의 `composer.json`은 여전히 다음을 읽을 수 있습니다.


```
"branch-alias": {
    "dev-master": "1.9.x-dev"
}
```

위의 내용은 다음과 같이 변경됩니다.

```
"branch-alias": {
    "dev-master": "1.10.x-dev"
}
```

이 변경 사항을 커밋합니다.

```
$ git commit -m "Master is now 1.10-dev" composer.json
```

## 배포

다음 단계에서는 유지 관리 분기(예시: `vX.Y` 브랜치를  X.Y.Z 로)의 릴리스 프로세스를 간략하게 설명합니다

### PHP 버전 호환성 보장

지원되는 버전의 PHP에서 라이브러리 테스트 그룹이 완료되는지 확인합니다.

### 전환 JIRA 문제 및 버전

릴리스 버전과 관련된 모든 문제는 "닫힘" 상태여야 합니다.
그리고 해상도는 "고정"입니다. 기타 해상도 문제(예:"중복", "설계한 대로 작동함")은 릴리스 노트에 나타나지 않기 위해 릴리스 버전에서 제거해야 합니다.

해당 ".x" 수정 버전을 확인하여 다음과 같은 문제가 있는지 확인합니다. 
이 릴리스 버전에 "고정됨"으로 해결된 문제가 포함되어 있을 경우, 이 릴리스 버전에 포함되어야 합니다.

해당 ".x" 수정 버전을 확인하여 "수정"으로 해결되었으며 이 릴리스 버전에 포함되어야 하는 문제가 있는지 확인합니다.

[Manage Versions](https://jira.mongodb.org/plugins/servlet/project-config/PHPLIB/versions) 페이지에서 버전의 릴리스 날짜 및 상태를 업데이트합니다.


### 버전 정보 업데이트

PHP 라이브러리는 [semantic versioning](http://semver.org/)를 사용한다. 
주요 버전이 아닌 릴리스에서 호환성을 중단하지 마십시오. 그렇지 않으면 너의 사용자들은 너를 죽일 수 있습니다.

계속하기 전에 '마스터' 분기가 모든 코드로 최신 상태인지 확인하십시오.
이 유지 관리 분기의 변경 사항. 이것은 나중에 병합된 커밋의 변경 사항을 무시한 `--strategy=ours`와 마스터까지 병합할 예정이기 때문에 중요합니다.

### 태그 배포

유지보수 부서의 HEAD가 릴리스 태그의 대상이 될 것입니다.

```
$ git tag -a -m "Release X.Y.Z" X.Y.Z
```

### 태그 푸시

```
$ git push --tags
```

### 유지 관리 분기를 마스터에 병합

```
$ git checkout master
$ git merge vX.Y --strategy=ours
$ git push
```

`--strategy=ours` 옵션을 사용하면 병합된 커밋의 모든 변경 사항은 무시됩니다.

### 릴리스 노트 게시

[다음 템플릿](https://github.com/mongodb/mongo-php-library/releases/new)을 사용하여 GitHub 릴리스 노트를 만들어야 합니다.

```
PHP 팀은 X.Y.Z 버전의 MongoDB PHP 라이브러리를 사용할 수 있게 된 것을 기쁘게 생각합니다.

**릴리스 하이라이트**

<이 릴리스의 중요한 변경사항을 설명하는 하나 이상의 단락>

이 릴리스의 해결된 문제에 대한 전체 목록은 다음 웹 사이트에서 확인할 수 있습니다:
$JIRA_URL

**문서화**

이 라이브러리에 대한 설명서는 다음 사이트에서 찾을 수 있습니다.
https://docs.mongodb.com/php-library/

**설치**

이 라이브러리는 다음과 같이 설치 또는 업그레이드할 수 있습니다:

    composer require mongodb/mongodb^X.Y.Z


`mongodb` 확장에 대한 설치 지침은 [PHP.net 설명서](https://www.php.net/manual/en/mongodb.installation.php)에서 확인할 수 있습니다.
```

해결된 JIRA 문제 목록의 URL은 각 릴리스에 따라 업데이트되어야 합니다.
목록은 [본 양식](https://jira.mongodb.org/secure/ReleaseNote.jspa?projectId=12483)에서 얻을 수 있습니다.


커뮤니티 기여자의 커밋이 이 릴리스에 포함된 경우 다음을 추가합니다.
해당 섹션:

```
**Thanks**

이 릴리스에 대한 커뮤니티 기여자분들께 감사드립니다:

 * [$CONTRIBUTOR_NAME](https://github.com/$GITHUB_USERNAME)
```
출시 공지도 [MongoDB 제품 & 드라이버 릴리스: 드라이버 릴리스](https://www.mongodb.com/community/forums/tags/c/announcements/driver-releases/110/php) 포럼에 게시하고 Twitter에서 공유해야 합니다.

### 새 주 버전과 부 버전에 대한 설명서 업데이트

새로운 주 릴리스 및 부 릴리스에서는 다른 릴리스에 대한 문서 업데이트도 필요합니다.
프로젝트:

 * DOCSP 티켓을 만들어 드라이버 문서 포털에 있는 PHP의 서버 및 언어 [호환성 테이블](https://docs.mongodb.com/drivers/php/#compatibility)에 새 버전을 추가합니다. 
   예제는 [mongodb/docs-ecosystem#642](https://github.com/mongodb/docs-ecosystem/pull/642)를 참조하십시오.

 * 라이브러리 [문서](https://docs.mongodb.com/php-library/)에서 "현재" 및 "신규" 탐색 링크를 업데이트하는 DOCSP 티켓을 만듭니다.
   [mongodb/docs-php-library](https://github.com/mongodb/docs-php-library)를 업데이트해야 합니다.

이러한 작업은 새 릴리스에 태그를 지정하기 전에 시작하여 릴리스가 게시된 후 즉시 업데이트된 컨텐츠에 액세스할 수 있도록 할 수 있습니다.

