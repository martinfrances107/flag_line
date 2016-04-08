# Flag Line
This is a Drupal8 module related the the flag module
see https://www.drupal.org/project/flag

# Scenario Testing

The goal of this module is to gain confidence in the integrity of flag
service and provide a testing environment to evaluate its performance.

### Properties of a scenario

  - A never ending sequence of random tasks to be performed by the service.

  - Scales in complexity - to stress the infrastructure under load.

  - The task should be pseudo randomly generated from a constant seed, to allow
    the performance of changes in the internals of the service to be compared.



  Confidence in the service under test grows as more tasks are completed
  correctly but the test will run until stopped by the user. The infrastructure
  under load could be an enterprise grade database server or humble a raspberry
  PI with a small memory card running a soak test.

# The Scenario: Animal Railway

   The railway operates using flags as tickets. Having animals as passengers
   allows for a variety of entity types to be flagged.

### The railway

  - A railway with 10 stations.

  - At regular intervals a batch animals is created and given a tickets to ride.
    [The animal type, and station source and destination are all assigned at
    random.]

  - At regular intervals eastbound and westbound train services are run
    to move passengers.

  - The life cycle of passengers and trains is predictable and finite.


##### Operations: When a train enters a station

  - All passengers boarding get their ticket flagged.
  - All passengers leaving get their ticket unflagged.
  - All passenger boarding, are flagged as being on the train.
  - All passenger leaving, are unflagged as being on the train.

##### Integrity checks

  The integrity checks are nothing more than those cross checks that happend on
  real train networks.


  - After a train has arrived at the last stop and unloads
  - there <em>should</em> be no passengers flagged as on the train.
  - The number of passenger on all trains <em>should</em> be equal to the
    number of tickets flagged.


##### Scaling

To increase the complexity - run more trains through more stations, with more
passengers.

##### Usage

If needed enable the module.

drush en flag_line

To create a new run with the name perf-test-1 using the console command

drupal flag_line:run perf-test-1

The run can be configured by overriding the default parameters

drupal flag_line:run --update_period 2 --num_stations 100 --num_passengers 1000 perf-test-1

