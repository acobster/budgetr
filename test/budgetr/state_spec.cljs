(ns budgetr.state-spec
  (:require
    [budgetr.state :as sut]
    [clojure.spec.alpha :as spec]
    [clojure.test.check.clojure-test :refer-macros [defspec]]
    [clojure.test.check.generators :as gen]
    [clojure.test.check.properties :as prop]))


;; TODO generate a bunch of app-states
;; pass them through various action handlers
;; assert they're in the right order, etc

(defn sorted-by-day? [items]
  (let [days (map (comp int :day) items)]
    (= days (sort days))))

(defspec update-item-sorts-items-by-day 25
  (prop/for-all
    [state (spec/gen ::budgetr.state/app-state)]
    (let [idx (rand-int (count (:items state)))
          item (gen/generate (spec/gen ::budgetr.state/item))
          new-state (sut/handle-action :update-item state idx item)
          new-items (:items new-state)]
    (and
     (sorted-by-day? new-items)
     (vector? new-items)))))

(defspec delete-item-removes-the-item 25
  (prop/for-all
   [state (spec/gen ::budgetr.state/app-state)]
   (let [old-items (:items state)
         idx (rand-int (count old-items))
         deleted-item (get old-items idx)
         new-state (sut/handle-action :delete-item state idx)
         new-items (:items new-state)]
      (and
       ; items should always be a vector
       (vector? new-items)
       ; there should be exactly one less than (count (:items state)) items left,
       ; unless there were none to begin with, in which case there should be zero
       (= (max 0 (dec (count old-items)))
          (count new-items))
       ; the new set of items should not contain the deleted item
       (not (contains? (set new-items) deleted-item))))))

(defspec create-item-inserts-new-item-at-specified-index 25
  (prop/for-all
   [state (spec/gen ::budgetr.state/app-state)
    item (spec/gen ::budgetr.state/item)]
   (let [idx (rand-int (count (:items state)))
         old-items (:items state)
         new-state (sut/handle-action :create-item state item idx)
         new-items (:items new-state)]
     (and
      (= (inc (count old-items)) (count new-items))
      (= item (get new-items idx))))))