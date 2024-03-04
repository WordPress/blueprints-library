<?php
//
//use WordPress\Blueprints\Progress\ProgressEvent;
//use WordPress\Blueprints\Progress\Tracker;
//
//describe( 'Tracks total progress', function () {
//	it( 'A single Tracker populated via set', function () {
//		$tracker = new Tracker();
//		$tracker->set( 50 );
//
//		expect( (int) $tracker->getProgress() )->toBe( 50 );
//	} );
//
//	it( 'Equally-weighted subtrackers should sum to 100 total progress after completion', function () {
//		$tracker  = new Tracker();
//		$partial1 = $tracker->stage( 1 / 3, 'Part 1' );
//		$partial2 = $tracker->stage( 1 / 3, 'Part 2' );
//		$partial3 = $tracker->stage( 1 / 3, 'Part 3' );
//
//		$partial1->set( 100 );
//		expect( (int) $tracker->getProgress() )->toBe( 33, 2 );
//
//		$partial2->set( 100 );
//		$partial2->set( 100 );
//		expect( (int) $tracker->getProgress() )->toBe( 66, 2 );
//
//		$partial3->set( 100 );
//		expect( (int) $tracker->getProgress() )->toBe( 100 );
//	} );
//
//	it( 'Differently-weighted subtrackers should sum to 100 total progress after completion', function () {
//		$tracker  = new Tracker();
//		$partial1 = $tracker->stage( 0.2, 'Part 1' );
//		$partial2 = $tracker->stage( 0.3, 'Part 2' );
//		$partial3 = $tracker->stage( 0.5, 'Part 3' );
//
//		$partial1->set( 100 );
//		expect( (int) $tracker->getProgress() )->toBe( 20 );
//
//		$partial2->set( 100 );
//		expect( (int) $tracker->getProgress() )->toBe( 50 );
//
//		$partial3->set( 100 );
//		expect( (int) $tracker->getProgress() )->toBe( 100 );
//	} );
//
//	describe( 'Subtrackers should sum to 100 total progress after completion even if the top-level tracker also has self-progress',
//		function () {
//			it( 'When subtrackers cover the entire progress range ', function () {
//				$tracker  = new Tracker();
//				$partial1 = $tracker->stage( 1 / 3, 'Part 1' );
//				$partial2 = $tracker->stage( 1 / 3, 'Part 2' );
//				$partial3 = $tracker->stage( 1 / 3, 'Part 3' );
//
//				$tracker->set( 100 );
//				$partial1->set( 100 );
//				$partial2->set( 100 );
//				$partial3->set( 100 );
//				expect( $tracker->getProgress() )->toBe( 100 );
//			} );
//			it( 'When subtrackers only cover 2/3 of the progress range ', function () {
//				$tracker  = new Tracker();
//				$partial1 = $tracker->stage( 1 / 3, 'Part 1' );
//				$partial2 = $tracker->stage( 1 / 3, 'Part 2' );
//
//				$tracker->set( 100 );
//				$partial1->set( 100 );
//				$partial2->set( 100 );
//				expect( (int) $tracker->getProgress() )->toBe( 100 );
//			} );
//		} );
//
//	it( 'Two levels of sub-trackers should sum to 100 total progress after completion', function () {
//		$tracker = new Tracker();
//		$level1A = $tracker->stage( 0.6, 'Level 1A' );
//		$level1B = $tracker->stage( 0.4, 'Level 1B' );
//
//		$level2A1 = $level1A->stage( 0.5, 'Level 2A1' );
//		$level2A2 = $level1A->stage( 0.5, 'Level 2A2' );
//		$level2B1 = $level1B->stage( 0.7, 'Level 2B1' );
//		$level2B2 = $level1B->stage( 0.3, 'Level 2B2' );
//		$level2A1->set( 100 );
//		expect( $tracker->getProgress() )->toBe( 30.0, 2 );
//
//		$level2A2->set( 100 );
//		expect( $tracker->getProgress() )->toBe( 60.0, 2 );
//
//		$level2B1->set( 100 );
//		expect( $tracker->getProgress() )->toBe( 88.0, 2 );
//
//		$level2B2->set( 100 );
//		expect( $tracker->getProgress() )->toBe( 100.0 );
//	} );
//} );
//
//describe( 'Events', function () {
//	it( 'Progress event emitted when using set', function () {
//		$tracker       = new Tracker();
//		$eventProgress = 0;
//
//		$tracker->events->addListener( 'progress', function ( ProgressEvent $event ) use ( &$eventProgress ) {
//			$eventProgress = $event->progress;
//		} );
//
//		$tracker->set( 50 );
//
//		expect( $eventProgress )->toBe( 50.0 );
//	} );
//
//	it( 'Progress event emitted when setting caption', function () {
//		$tracker      = new Tracker();
//		$eventCaption = '';
//
//		$tracker->events->addListener( 'progress', function ( ProgressEvent $event ) use ( &$eventCaption ) {
//			$eventCaption = $event->caption;
//		} );
//
//		$tracker->setCaption( 'Test caption' );
//
//		expect( $eventCaption )->toBe( 'Test caption' );
//	} );
//
//	it( 'Progress event emitted for sub trackers', function () {
//		$tracker       = new Tracker();
//		$partial1      = $tracker->stage( 0.5, 'Part 1' );
//		$eventProgress = 0;
//
//		$tracker->events->addListener( 'progress', function ( ProgressEvent $event ) use ( &$eventProgress ) {
//			$eventProgress = $event->progress;
//		} );
//
//		$partial1->set( 100 );
//
//		expect( $eventProgress )->toBe( 50.0 );
//	} );
//} );
//
//describe( 'Caption management', function () {
//	it( 'Should return caption of a Tracker', function () {
//		$tracker = new Tracker( [ "caption" => 'Initial caption' ] );
//
//		expect( $tracker->getCaption() )->toBe( 'Initial caption' );
//	} );
//
//	it( 'Should return the updated caption of a single Tracker', function () {
//		$tracker = new Tracker( [ "caption" => 'Initial caption' ] );
//		$tracker->setCaption( 'Updated caption' );
//
//		expect( $tracker->getCaption() )->toBe( 'Updated caption' );
//	} );
//
//	it( 'Should return caption of the most recently started sub tracker', function () {
//		$tracker = new Tracker();
//		$tracker->stage( 0.5, 'Part 1' );
//		$tracker->stage( 0.5, 'Part 2' );
//		expect( $tracker->getCaption() )->toBe( 'Part 2' );
//	} );
//
//	it( 'Should return caption of the most recently started sub tracker – multi-level', function () {
//		$tracker  = new Tracker();
//		$partial1 = $tracker->stage( 0.5, 'Part 1' );
//		expect( $tracker->getCaption() )->toBe( 'Part 1' );
//
//		$partial1->stage( 0.5, 'Part 1.a' );
//		expect( $tracker->getCaption() )->toBe( 'Part 1.a' );
//
//		$partial1->stage( 0.5, 'Part 1.b' );
//		expect( $tracker->getCaption() )->toBe( 'Part 1.b' );
//
//		$partial2 = $tracker->stage( 0.5, 'Part 2' );
//		expect( $tracker->getCaption() )->toBe( 'Part 2' );
//
//		$partial2->stage( 0.5, 'Part 2.a' );
//		expect( $tracker->getCaption() )->toBe( 'Part 2.a' );
//
//		$partial2->stage( 0.5, 'Part 2.b' );
//		expect( $tracker->getCaption() )->toBe( 'Part 2.b' );
//	} );
//
//	it( 'Should return caption of the most recent incomplete sub tracker – multi-level', function () {
//		$tracker  = new Tracker();
//		$partial1 = $tracker->stage( 0.5, 'Part 1' );
//		expect( $tracker->getCaption() )->toBe( 'Part 1' );
//
//		$partial1a = $partial1->stage( 0.5, 'Part 1.a' );
//		$partial1b = $partial1->stage( 0.5, 'Part 1.b' );
//		$partial2  = $tracker->stage( 0.5, 'Part 2' );
//		$partial2a = $partial2->stage( 0.5, 'Part 2.a' );
//		$partial2b = $partial2->stage( 0.5, 'Part 2.b' );
//
//		$partial2b->set( 100 );
//		expect( $tracker->getCaption() )->toBe( 'Part 2.a' );
//
//		$partial2a->set( 100 );
//		expect( $partial2->isDone() )->toBe( true );
//		expect( $tracker->getCaption() )->toBe( 'Part 1.b' );
//
//		$partial1a->set( 100 );
//		expect( $tracker->getCaption() )->toBe( 'Part 1.b' );
//
//		$partial1b->set( 100 );
//		expect( $tracker->getCaption() )->toBe( '' );
//	} );
//
//	it( 'Should return caption of the most recently started and not completed sub tracker', function () {
//		$tracker = new Tracker();
//		$tracker->stage( 0.5, 'Part 1' );
//		$partial2 = $tracker->stage( 0.5, 'Part 2' );
//
//		$partial2->set( 95 );
//		expect( $tracker->getCaption() )->toBe( 'Part 2' );
//
//		$partial2->set( 100 );
//		expect( $tracker->getCaption() )->toBe( 'Part 1' );
//	} );
//
//	it( 'Should return no caption when all sub trackers are completed', function () {
//		$tracker  = new Tracker();
//		$partial1 = $tracker->stage( 0.5, 'Part 1' );
//		$partial2 = $tracker->stage( 0.5, 'Part 2' );
//
//		$partial1->set( 100 );
//		$partial2->set( 100 );
//
//		expect( $tracker->getCaption() )->toBe( '' );
//	} );
//} );
