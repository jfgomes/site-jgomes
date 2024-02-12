![Git flow diagram](https://qph.cf2.quoracdn.net/main-qimg-729a22aba98d1235fdce4883accaf81e)

## Introduction

- This project uses Git-Hub as de repo provider.

- The main branch is master.

- There's a configured rule for master, like:

![git-branch-protection.png](https://jgomes.site/images/cs/git-branch-protection.png)

and..

![git-branch-protection-setup.png](https://jgomes.site/images/cs/git-branch-protection-setup.png)

- There's also a CODEOWNERS file in the project.

![git-branch-protection-codeowners](https://jgomes.site/images/cs/git-branch-protection-codeowners.png)

- This codeowners file above ensures every change done on the code needs to have the approval of the user @jfgomes. Otherwise, the code is never pushed to master.

## Defined Git rules

- The master branch is blocked for direct pushes. Nobody can push directly to it.


- Push to master is only allowed after approved PR's and is done via Git-Hub site.


- Every developer needs to create a new branch to change the code and create a PR to push the changes to master only after PR is approved.


- New branches can have the following prefixes: feature/xxxxx || bugfix/xxxxx || fix/xxxxx || enhancement/xxxxx.


- There´s a codeowners file on the project where GitHub will read after the PR created and will ask for approve of the users configured inside that.


- After the PR approved and the push to master is done, the web-hook to Jenkins will trigger the CI / CD to start.


- If the deploy is done successfully, delete the new branch.


- Update the Master branch before create another new branch.


- Iterate..

## Git diagram

![Git flow diagram](https://jgomes.site/images/diagrams/git.drawio.png)

## Set CODEOWNERS

- Create a simple file ( without extension ) called CODEOWNERS inside a dir called .github at the base of the project.

![git-branch-protection-codeowners](https://jgomes.site/images/cs/git-branch-protection-create-codeowners.png)

- Create a new rule in GitHub ( See the introduction block before ).


- Test and check if it works - check the video under ( video with a git simple example, where I have a code owner @jfgomes and a contributor user that is not on the code owner's list called jfgomes2 ):

## Demonstration ( click on the image to see the video )
[![Demonstration video](https://jgomes.site/images/cs/git-branch-protection-video-thumbnail.jpg)](http://www.youtube.com/watch?v=6bGltddfJIM)
