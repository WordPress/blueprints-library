<?php

namespace WordPress\Blueprints\Progress;

use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * The ProgressTracker class is a tool for tracking progress in an operation that is
 * divided into multiple stages. It allows you to create sub-trackers for each stage,
 * with individual weights and captions. The main tracker automatically calculates the
 * progress based on the weighted sum of each sub-tracker's progress. This makes it easy
 * to keep track of a complex, multi-stage process and report progress in a user-friendly way.
 *
 * After creating the sub-trackers, you can call the set() method to update the progress
 * of the current stage. You can also call the finish() method to mark the current stage
 * as complete and move on to the next one. Alternatively, you can call the fillSlowly()
 * method to simulate progress filling up slowly to 100% before calling finish().
 *
 * @example
 * ```ts
 * const tracker = new ProgressTracker();
 * tracker.addEventListener('progress', (e) => {
 *        console.log(
 *                e.detail.progress,
 *                e.detail.caption
 *        );
 * });
 *
 * const stage1 = tracker.stage(0.5, 'Calculating pi digits');
 * const stage2 = tracker.stage(0.5, 'Downloading data');
 *
 * stage1.fillSlowly();
 * await calc100DigitsOfPi();
 * stage1.finish();
 *
 * await fetchWithProgress(function onProgress(loaded, total) {
 *        stage2.set( loaded / total * 100);
 * });
 * stage2.finish();
 */
class Tracker {
	private $selfWeight = 1;
	private $selfDone = false;
	private $selfProgress = 0;
	private $selfCaption = '';
	private $weight;
	private $subTrackers = [];

	public EventDispatcher $events;

	public function __construct( $options = [] ) {
		$this->weight = $options['weight'] ?? 1;
		$this->selfCaption = $options['caption'] ?? '';
		$this->events = new EventDispatcher();
	}

	/**
	 * Creates a new sub-tracker with a specific weight.
	 *
	 * The weight determines what percentage of the overall progress
	 * the sub-tracker represents. For example, if the main tracker is
	 * monitoring a process that has two stages, and the first stage
	 * is expected to take twice as long as the second stage, you could
	 * create the first sub-tracker with a weight of 0.67 and the second
	 * sub-tracker with a weight of 0.33.
	 *
	 * The caption is an optional string that describes the current stage
	 * of the operation. If provided, it will be used as the progress caption
	 * for the sub-tracker. If not provided, the main tracker will look for
	 * the next sub-tracker with a non-empty caption and use that as the progress
	 * caption instead.
	 *
	 * Returns the newly-created sub-tracker.
	 *
	 * @param weight The weight of the new stage, as a decimal value between 0 and 1.
	 * @param caption The caption for the new stage, which will be used as the progress caption for the sub-tracker.
	 *
	 * @throws {Error} If the weight of the new stage would cause the total weight of all stages to exceed 1.
	 *
	 * @example
	 * ```ts
	 * const tracker = new ProgressTracker();
	 * const subTracker1 = tracker.stage(0.67, 'Slow stage');
	 * const subTracker2 = tracker.stage(0.33, 'Fast stage');
	 *
	 * subTracker2.set(50);
	 * subTracker1.set(75);
	 * subTracker2.set(100);
	 * subTracker1.set(100);
	 * ```
	 */
	public function stage( $weight = null, $caption = '' ) {
		if ( ! $weight ) {
			$weight = $this->selfWeight;
		}
		if ( $this->selfWeight - $weight < - 0.00001 ) {
			throw new \Exception(
				'Cannot add a stage with weight ' . $weight . ' as the total weight of registered stages would exceed 1.'
			);
		}
		$this->selfWeight -= $weight;

		$subTracker = new Tracker( [
			'caption' => $caption,
			'weight'  => $weight,
		] );
		$this->subTrackers[] = $subTracker;
		$subTracker->events->addListener( ProgressEvent::class, function () {
			$this->notifyProgress();
		} );
		$subTracker->events->addListener( DoneEvent::class, function () {
			if ( $this->isDone() ) {
				$this->notifyDone();
			}
		} );

		return $subTracker;
	}

	public function set( float $value, ?string $caption = null ): void {
		if ( $value < $this->selfProgress ) {
			throw new \InvalidArgumentException( "Progress cannot go backwards (tried updating to $value when it already was $this->selfProgress)" );
		}
		// Don't report the same progress twice
		if ( $this->selfProgress === $value && ( $caption === null || $this->selfCaption === $caption ) ) {
			return;
		}
		$this->selfProgress = min( $value, 100 );
		if ( $caption !== null ) {
			$this->selfCaption = $caption;
		}
		$this->notifyProgress();
		if ( $this->selfProgress + 0.00001 >= 100 ) {
			$this->finish();
		}
	}

	public function setCaption( $caption ) {
		$this->selfCaption = $caption;
		$this->notifyProgress();
	}

	public function finish() {
		$this->selfDone = true;
		$this->selfProgress = 100;
		$this->notifyProgress();
		$this->notifyDone();
	}

	public function getCaption() {
		foreach ( $this->subTrackers as $subTracker ) {
			if ( ! $subTracker->isDone() ) {
				return $subTracker->getCaption();
			}
		}

		return $this->selfCaption;
	}


	public function isDone() {
		return $this->getProgress() + 0.00001 >= 100;
	}

	public function getProgress() {
		if ( $this->selfDone ) {
			return 100;
		}
		$sum = array_reduce( $this->subTrackers, function ( $sum, $tracker ) {
			return $sum + $tracker->getProgress() * $tracker->getWeight();
		}, $this->selfProgress * $this->selfWeight );

		return round( $sum * 10000 ) / 10000;
	}

	public function getWeight() {
		return $this->weight;
	}

	private function notifyProgress() {
		$this->events->dispatch(
			new ProgressEvent(
				$this->getProgress(),
				$this->getCaption(),
			)
		);
	}

	private function notifyDone() {
		$this->events->dispatch( new DoneEvent() );
	}

}
