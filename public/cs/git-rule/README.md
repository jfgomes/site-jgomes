![Git flow diagram](https://qph.cf2.quoracdn.net/main-qimg-729a22aba98d1235fdce4883accaf81e)

## Introduction

- This project uses Git-Hub as de repo provider.

- The main branch is master.

- There's a configured rule for master.

![git-branch-protection.png](https://jgomes.site/images/cs/git-branch-protection.png)

and..

![git-branch-protection-setup.png](https://jgomes.site/images/images/cs/git-branch-protection-setup.png)

- There's a CODEOWNERS file in the project.

![git-branch-protection-codeowners](https://jgomes.site/images/cs/git-branch-protection-codeowners.png)

- This codeowners file above means every change on the code needs to have the approval of the user @jfgomes. Otherwise the code is never pushed to master.

## Git rule notes

- The master branch is blocked for pushes. Nobody can push directly to it.


- Push to master is only allowed after approved PR's.


- Every developer needs to create a new branch to change the code and create a PR after to master.


- New branches can have the following prefixes: Feature/xxxxx Bugfix/xxxxx Fix/xxxxx Enhancement/xxxxx.


- There´s a codeowners file on the project where GitHub will read after the PR created and will ask for approve of the users configured inside that.


- After the PR approved and the push to master is done, the web-hook to Jenkins will trigger the CI / CD to start.


- If the deploy is done successfully, delete the new branch.


- Update the Master branch before create another new branch.


- Iterate..

## Git diagram

![Git flow diagram](https://jgomes.site/images/diagrams/git.drawio.png)

## Set CODEOWNERS 

- Create a simple file ( without ext ) called CODEOWNERS at the base of the project -> .github -> CODEOWNERS

![git-branch-protection-codeowners](https://jgomes.site/images/cs/git-branch-protection-create-codeowners.png)

- Create a new rule in GitHub ( See the introduction before ).

- Test and check if it works - check the video under ( using a git sample for this project, where I have a code owner @jfgomes and a contributor user that is not on the code owner's list called jfgomes2 ):

## Demonstration ( video )
[![Demonstration video](http://img.youtube.com/vi/6bGltddfJIM/0.jpg)](http://www.youtube.com/watch?v=6bGltddfJIM)
