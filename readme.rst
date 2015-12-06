######################
Stepmania Leaderboards
######################

This repository contains all code currently running on http://smleaderboards.net. This repository is intended for informational purposes relating to that project.

The goal of the project was to create an algorithm that would interpret the difficulty of various StepMania charts with relative accuracy. This poses a complex problem as charts can vary wildly, and a simple interpretation of the file is simply not enough to determine how difficult any given patterns in the files might actually be. The algorithms contained within attempt to make sense and create mathematical formulas to interpret these patterns, in a way that will correlate to the overal difficulty of the file.

In short - the heart of this project lies in /application/core/MY_Controller.php, where we do all of the parsing and file analysis. As this algorithm has grown, many functions within have gotten a little spaghettified. A thorough refactoring is needed soon. The scripts are also very memory and CPU intensive, so any hopes of running this on a server will require some server configuration, and a powerful enough machine to complete the requests in a timely manner. At the current iteraction of this calculator, our max expected execution time for even very long files should be no longer than 10 seconds. This is achieved through a modified binary search function used when calculating the final difficulty values.

Beyond the calculator, within is a full fledged, database driven site, containing features like user registration and logins, profile editing, the ability to upload scores, search through the list of existing files, and much more. A basic chat interface is included in the home page to promote engagement. Moderators and Admins have aditional controls, such as the ability to add files, packs, approve top-end scores, perform batch recalculations, and more. 

The end result of all this - we now have a fairly accurate leaderboards system, not without its margin of error, that can be used by the community to see how they compare to everyone else in terms of personal skill level. To my knowledge, such a comprehensive system has not existed before this, or at least not one with any semblence of accuracy or reliability.
